<?php

namespace Mattoid\StoreInvite\Goods;

use Mattoid\Store\Goods\Invalid;
use Mattoid\Store\Model\StoreCartModel;
use Mattoid\Store\Model\StoreModel;

class InviteInvalid extends Invalid
{

    public static function invalid(StoreModel $store, StoreCartModel $cart) {
        app('log')->info('商品失效业务逻辑');
    }
}
