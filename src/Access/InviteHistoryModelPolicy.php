<?php

namespace Mattoid\StoreInvite\Access;

use Flarum\User\Access\AbstractPolicy;
use Flarum\User\User;
use Mattoid\StoreInvite\Model\InviteHistoryModel;

class InviteHistoryModelPolicy extends AbstractPolicy
{
    public function can(User $actor, string $ability, InviteHistoryModel $model)
    {
        // See https://docs.flarum.org/2.x/extend/authorization.html#custom-policies for more information.
    }
}
