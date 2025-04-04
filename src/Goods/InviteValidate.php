<?php

namespace Mattoid\StoreInvite\Goods;

use Carbon\Carbon;
use Flarum\Foundation\ValidationException;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Exception\PermissionDeniedException;
use Flarum\User\User;
use Illuminate\Contracts\Cache\Repository;
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

        $groupIds = array_column(json_decode(json_encode($user->groups)), 'id');
        if ($user->can('mattoid-store-invite.group-blacklist-view') && !in_array('1', $groupIds)) {
            throw new PermissionDeniedException();
            return false;
        }

        $cache = resolve(Repository::class);
        $settings = resolve(SettingsRepositoryInterface::class);
        $translator = resolve(TranslatorInterface::class);

        if (!$params['email']) {
            throw new ValidationException(['message' => $translator->trans('mattoid-store-invite.forum.error.email-not-exist')]);
        }
        if (!$params['link']) {
            throw new ValidationException(['message' => $translator->trans('mattoid-store-invite.forum.error.link-not-exist')]);
        }

        $key = md5("confirm-{$params['email']}");
        if (!$cache->add($key, $params['email'], 30)) {
            throw new ValidationException(['message' => $translator->trans('mattoid-store-invite.forum.error.lock-resources')]);
        }

        $storeTimezone = $settings->get('mattoid-store.storeTimezone', 'Asia/Shanghai');
        $settingTimezone = !!$storeTimezone ? $storeTimezone : 'Asia/Shanghai';

        $invite = InviteModel::query()->where('user_id', $user->id)->where('status', 0)->first();
        if ($invite) {
            throw new ValidationException(['message' => $translator->trans('mattoid-store-invite.forum.error.unaudited')]);
        }

        $time = Carbon::now()->subDays($settings->get('mattoid-store-invite.calm-down-period', 0))->tz($settingTimezone);
        $invite = InviteModel::query()->where('user_id', $user->id)->where('confirm_time', '>=', $time)->where('status', 2)->orderByDesc('created_at')->first();
        if ($invite) {
            $date = Carbon::parse($invite->confirm_time)->addDays($settings->get('mattoid-store-invite.calm-down-period', 0))->tz($settingTimezone);
            throw new ValidationException(['message' => $translator->trans('mattoid-store-invite.forum.error.review-failed', ['date' => $date])]);
        }

        $timeSuccess = Carbon::now()->subDays($settings->get('mattoid-store-invite.invite-calm-down-period', 0))->tz($settingTimezone);
        $inviteSuccess = InviteModel::query()->where('user_id', $user->id)->where('confirm_time', '>=', $timeSuccess)->where('status', 1)->orderByDesc('created_at')->first();
        if ($inviteSuccess) {
            $date = Carbon::parse($inviteSuccess->confirm_time)->addDays($settings->get('mattoid-store-invite.invite-calm-down-period', 0))->tz($settingTimezone);
            throw new ValidationException(['message' => $translator->trans('mattoid-store-invite.forum.error.review-success', ['date' => $date])]);
        }

        // 黑名单用户
        if (class_exists('\Mattoid\Blacklist\Model\BlacklistModel')) {
            $blacklist = \Mattoid\Blacklist\Model\BlacklistModel::query()->where('email', $params['email'])->first();
            if ($blacklist) {
                throw new ValidationException(['message' => $translator->trans('mattoid-store-invite.forum.error.blacklist')]);
            }
        }

        // 受邀人是否存在
        $user = User::query()->where(function($where) use ($params) {
            $where->where('email', $params['email']);
            if (class_exists('Mattoid\SecondEmail\Search\SecondEmailSearch')) {
                $where->orWhere('second_email', $params['email']);
            }
        })->first();
        if ($user) {
            throw new ValidationException(['message' => $translator->trans('mattoid-store-invite.forum.error.email-exist')]);
        }

        $inviteValidate = InviteModel::query()->where('email', $params['email'])->whereIn('status', [0,1])->where('is_expire', 0)->first();
        if ($inviteValidate) {
            throw new ValidationException(['message' => $translator->trans('mattoid-store-invite.forum.error.email-exist')]);
        }

        // 会员活跃度


        return true;
    }
}
