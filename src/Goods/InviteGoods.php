<?php

namespace Mattoid\StoreInvite\Goods;

use Mattoid\Store\Goods\Goods;

class InviteGoods extends Goods
{
    public $code = 'invite';
    public $name = 'mattoid-store-invite.forum.title';
    public $uri = '/store/buy/invite';
    public $method = 'POST';
    public $className = 'store-buy Modal--small';
    public $popUp = [
        [
            'label' => 'mattoid-store-invite.forum.email',
            'prop' => 'input',
            'type' => 'email',
            'value' => 'email',
            'helpText' => 'mattoid-store-invite.forum.email-help'
        ],
        [
            'label' => 'mattoid-store-invite.forum.username',
            'prop' => 'input',
            'type' => 'text',
            'value' => 'username',
            'helpText' => 'mattoid-store-invite.forum.username-help'
        ],
        [
            'label' => 'mattoid-store-invite.forum.link',
            'prop' => 'input',
            'type' => 'text',
            'value' => 'link',
            'helpText' => 'mattoid-store-invite.forum.link-help'
        ],
        [
            'label' => 'mattoid-store-invite.forum.link-2',
            'prop' => 'input',
            'type' => 'text',
            'value' => 'link2',
            'helpText' => 'mattoid-store-invite.forum.link-2-help'
        ],
        [
            'label' => 'mattoid-store-invite.forum.remark',
            'prop' => 'textarea',
            'type' => 'text',
            'value' => 'applyRemark',
            'helpText' => 'mattoid-store-invite.forum.remark-help'
        ],


//        [
//            'label' => 'mattoid-store-invite.forum.remark',
//            'prop' => 'select',
//            'options' => [
//                '0' => 'aaaaaa',
//                '1' => 'bbbbbb',
//            ],
//            'value' => 'select',
//            'helpText' => 'mattoid-store-invite.forum.remark-help'
//        ],
//        [
//            'label' => 'mattoid-store-invite.forum.remark',
//            'prop' => 'switch',
//            'type' => 'text',
//            'value' => 'switch',
//            'helpText' => 'mattoid-store-invite.forum.remark-help'
//        ],
//        [
//            'label' => 'mattoid-store-invite.forum.remark',
//            'prop' => 'input',
//            'type' => 'number',
//            'value' => 'test',
//            'helpText' => 'mattoid-store-invite.forum.remark-help'
//        ],
    ];

}
