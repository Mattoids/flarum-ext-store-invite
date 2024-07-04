<?php

namespace Mattoid\StoreInvite\Event\Event;

use Flarum\User\User;
use Mattoid\StoreInvite\Model\InviteModel;

class InviteEvent
{

    public $user;
    public $invite;

    public function __construct(User $user = null, InviteModel $invite) {
        $this->user = $user;
        $this->invite = $invite;
    }
}
