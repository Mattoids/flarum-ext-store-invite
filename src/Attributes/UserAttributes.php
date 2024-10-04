<?php

namespace Mattoid\StoreInvite\Attributes;

use Flarum\Api\Serializer\BasicUserSerializer;
use Flarum\Post\Post;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Symfony\Contracts\Translation\TranslatorInterface;

class
UserAttributes
{
    /**
     * @var SettingsRepositoryInterface|mixed
     */
    private $settings;

    /**
     * @var mixed|TranslatorInterface
     */
    private $translator;

    private $post;

    public function __construct()
    {
        $this->settings = resolve(SettingsRepositoryInterface::class);
        $this->translator = resolve(TranslatorInterface::class);
    }


    public function __invoke(BasicUserSerializer $serializer, User $user) {
        $attributes = [];
        $actor = $serializer->getActor();

        $canViewButton = $actor->can('mattoid-store-invite.group-view');
        $blacklist = $actor->can('mattoid-store-invite.group-blacklist-view');
        $canAdminViewButton = $actor->can('mattoid-store-invite.group-admin-view');

        if (!$blacklist) {
            $attributes['canInviteView'] = $canViewButton;
        } else {
            $attributes['canInviteView'] = false;
        }
        $attributes['canInviteAdminView'] = $canAdminViewButton;

        return $attributes;
    }
}
