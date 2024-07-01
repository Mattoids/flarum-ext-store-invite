<?php

namespace Mattoid\StoreInvite\Goods;

use Carbon\Carbon;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Mattoid\Store\Goods\After;
use Mattoid\Store\Model\StoreModel;
use Mattoid\Store\Utils\ObjectsUtil;
use Mattoid\StoreInvite\Model\InviteModel;

class InviteAfter extends After
{

    /**
     * 后置事件，处理失败则自动回滚购买逻辑
     * @param User $user
     * @param StoreModel $store
     * @param $params
     * @return boolean true-处理成功 false-失败回滚
     */
    public static function after(User $user, StoreModel $store, $params) {

        $settings = resolve(SettingsRepositoryInterface::class);

        $insert = ObjectsUtil::removeEmptySql($params);
        $insert['user_id'] = $user->id;
        $insert['status'] = 0;
        $insert['created_at'] = Carbon::now()->tz($settings->get('mattoid-store.storeTimezone','Asia/Shanghai'));
        $insert['updated_at'] = Carbon::now()->tz($settings->get('mattoid-store.storeTimezone','Asia/Shanghai'));
        unset($insert['id']);

        InviteModel::query()->insert($insert);

        return true;
    }
}
