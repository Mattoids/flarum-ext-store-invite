<?php

namespace Mattoid\StoreInvite\Helpers;

use Carbon\Carbon;
use Flarum\Foundation\ValidationException;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Mattoid\StoreInvite\Event\InviteEvent;
use Mattoid\StoreInvite\Model\InviteModel;
use Mattoid\StoreInvite\Utils\StringUtil;
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

        // 审核通过
        if ($params['status'] == 1) {
            $invite->invite_code = StringUtil::getInviteCode($invite->user_id);
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

            // 发送邀请码
            $events->dispatch(new InviteEvent($user, $invite));
        }

        $invite->confirm_user_id = $actor->id;
        $invite->confirm_remark = $params['confirmRemark'];
        $invite->status = $params['status'];
        $invite->confirm_time = Carbon::now()->tz($settings->get('mattoid-store.storeTimezone', 'Asia/Shanghai') ?? 'Asia/Shanghai');
        $invite->updated_at = Carbon::now()->tz($settings->get('mattoid-store.storeTimezone', 'Asia/Shanghai') ?? 'Asia/Shanghai');
        $invite->save();

        $cache->delete($key);
    }

}
