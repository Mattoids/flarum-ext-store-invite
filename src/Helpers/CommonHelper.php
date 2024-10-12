<?php

namespace Mattoid\StoreInvite\Helpers;

use Carbon\Carbon;
use Flarum\Foundation\ValidationException;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Flarum\Group\Group;
use FoF\Doorman\Doorkey;
use FoF\Doorman\Commands\DeleteDoorkey;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Mattoid\StoreInvite\Event\InviteEvent;
use Mattoid\StoreInvite\Model\InviteModel;
use Symfony\Contracts\Translation\TranslatorInterface;

class CommonHelper
{
    public static function confirm(User $actor, $params, InviteModel $invite)
    {
        $key = md5("confirm-{$invite->email}-{$invite->user_id}");
        $cache = resolve(Repository::class);
        $events = resolve(Dispatcher::class);
        $settings = resolve(SettingsRepositoryInterface::class);
        $translator = resolve(TranslatorInterface::class);

        $storeTimezone = $settings->get('mattoid-store.storeTimezone', 'Asia/Shanghai');
        $settingTimezone = !!$storeTimezone ? $storeTimezone : 'Asia/Shanghai';

        // 审核通过
        if ($params['status'] == 1) {
            if (!$cache->add($key, $invite->invite_code, 5)) {
                throw new ValidationException(['message' => $translator->trans('mattoid-store-invite.forum.error.lock-resources')]);
            }

            // 扣费
            $user = User::query()->where('id', $invite->user_id)->first();
            $price = $settings->get('mattoid-store-invite.price', 0);
            $money = $user->money;
            $balance = $money - $price;
            if ($balance < 0) {
                throw new ValidationException(['message' => $translator->trans('mattoid-store-invite.forum.error.user-balance-low')]);
            }

            $user->money = $balance;
            $user->where('money', $money);
            if (!$user->save()) {
                throw new ValidationException(['message' => $translator->trans('mattoid-store-invite.forum.error.user-balance-low')]);
            }

            // 通知资金消费记录插件
            if (class_exists('Mattoid\MoneyHistory\Event\MoneyHistoryEvent')) {
                $events->dispatch(new \Mattoid\MoneyHistory\Event\MoneyHistoryEvent($user, -$price, 'CONFIRMINVITE', $translator->trans("mattoid-store-invite.forum.confirm-invite-price"), ''));
            }

            // 创建邀请码
            $doorkey = Doorkey::build($invite->invite_code, $settings->get('mattoid-store-invite.group', Group::MEMBER_ID), 1, false);
            $doorkey->save();


            // 发送邀请码
            $invite->doorkey_id = $doorkey->id;
            $events->dispatch(new InviteEvent($user, $invite));
        }

        $invite->confirm_user_id = $actor->id;
        $invite->confirm_remark = $params['confirmRemark'];
        $invite->status = $params['status'];
        $invite->confirm_time = Carbon::now()->tz($settingTimezone);
        $invite->updated_at = Carbon::now()->tz($settingTimezone);
        $invite->save();

        $cache->delete($key);
    }

    public static function delete(User $actor, InviteModel $invite)
    {
        Doorkey::query()->where('id', $invite->doorkey_id)->delete();
        $invite->is_expire = 1;
        $invite->save();
    }
}
