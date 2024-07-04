<?php

/*
 * This file is part of mattoid/flarum-ext-store-invite.
 *
 * Copyright (c) 2024 mattoid.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use Flarum\Api\Serializer\BasicUserSerializer;
use Flarum\Extend;
use Mattoid\Store\Event\StoreBuyEvent;
use Mattoid\Store\Event\StoreInvalidEvent;
use Mattoid\Store\Extend\StoreExtend;
use Mattoid\StoreInvite\Attributes\UserAttributes;
use Mattoid\StoreInvite\Console\Command\AutoReviewCommand;
use Mattoid\StoreInvite\Console\PublishSchedule;
use Mattoid\StoreInvite\Controller\EditInviteConfirmController;
use Mattoid\StoreInvite\Controller\ListInviteApplyController;
use Mattoid\StoreInvite\Event\Event\InviteEvent;
use Mattoid\StoreInvite\Goods\InviteAfter;
use Mattoid\StoreInvite\Goods\InviteGoods;
use Mattoid\StoreInvite\Goods\InviteInvalid;
use Mattoid\StoreInvite\Goods\InviteValidate;
use Mattoid\StoreInvite\Listeners\InviteListeners;
use Mattoid\StoreInvite\Listeners\StoreInvalidListeners;
use Mattoid\StoreInvite\Listeners\StoreInviteListeners;
use Mattoid\StoreInvite\Middleware\RegistrationInterceptMiddleware;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/less/forum.less')
        ->route('/invite', 'mattoid-store-invite.forum.invite')
        ->route('/u/{username}/invite', 'mattoid-store-invite.forum.my-invite'),
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/less/admin.less'),
    new Extend\Locales(__DIR__.'/locale'),

    (new Extend\Middleware("api"))
        ->add(RegistrationInterceptMiddleware::class),

    (new StoreExtend('invite'))
        ->addStoreGoods(InviteGoods::class)
        ->addValidate(InviteValidate::class)
        ->addAfter(InviteAfter::class)
        ->addInvalid(InviteInvalid::class),

    (new Extend\Event())
        ->listen(InviteEvent::class, InviteListeners::class)
        ->listen(StoreBuyEvent::class, StoreInviteListeners::class)
        ->listen(StoreInvalidEvent::class, StoreInvalidListeners::class),

    (new Extend\ApiSerializer(BasicUserSerializer::class))
        ->attributes(UserAttributes::class),

    (new Extend\Settings())
        ->serializeToForum('inviteShowIndex', 'mattoid-store-invite.show-index'),

    (new Extend\Routes('api'))
        ->get('/store/invite/list', 'store.invite.list', ListInviteApplyController::class)
        ->put('/store/invite/edit', 'store.invite.edit', EditInviteConfirmController::class),

    (new Extend\Console())
        ->command(AutoReviewCommand::class)
        ->schedule(AutoReviewCommand::class, new PublishSchedule()),
];
