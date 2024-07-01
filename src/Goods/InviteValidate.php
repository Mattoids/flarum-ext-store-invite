<?php

namespace Mattoid\StoreInvite\Goods;

use Carbon\Carbon;
use Flarum\Foundation\ValidationException;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Exception\PermissionDeniedException;
use Flarum\User\User;
use Mattoid\Store\Goods\Validate;
use Mattoid\Store\Model\StoreModel;
use Mattoid\StoreInvite\Model\InviteModel;
use Symfony\Contracts\Translation\TranslatorInterface;

class InviteValidate extends Validate
{

    /**
     * 前置校验，用于购买商品前验证用户是否允许购买等逻辑
     * @param User $user
     * @param StoreModel $store
     * @param $params
     * @return true
     * @throws PermissionDeniedException
     */
    public static function validate(User $user, StoreModel $store, $params) {
        if (!$user->can('mattoid-store-invite.group-view')) {
            throw new PermissionDeniedException();
            return false;
        }

        $settings = resolve(SettingsRepositoryInterface::class);
        $translator = resolve(TranslatorInterface::class);

        $invite = InviteModel::query()->where('user_id', $user->id)->where('status', 0)->first();
        if ($invite) {
            throw new ValidationException(['message' => $translator->trans('mattoid-store-invite.forum.error.unaudited')]);
        }

        $time = Carbon::now()->subDays($settings->get('mattoid-store-invite.calm-down-period', 0))->tz($settings->get('mattoid-store.storeTimezone','Asia/Shanghai'));
        $invite = InviteModel::query()->where('user_id', $user->id)->where('confirm_time', '>=', $time)->orderByDesc('created_at')->first();
        if ($invite) {
            $date = Carbon::parse($invite->confirm_time)->addDays($settings->get('mattoid-store-invite.calm-down-period', 0))->tz($settings->get('mattoid-store.storeTimezone','Asia/Shanghai'));
            throw new ValidationException(['message' => $translator->trans('mattoid-store-invite.forum.error.review-failed', ['date' => $date])]);
        }

        // 受邀人是否存在
        $user = User::query()->where('email', $params['email'])->orWhere('second_email', $params['email'])->first();
        if ($user) {
            throw new ValidationException(['message' => $translator->trans('mattoid-store-invite.forum.error.email-exist')]);
        }

        // 会员活跃度


        return true;
    }
}
