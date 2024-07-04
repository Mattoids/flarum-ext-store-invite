<?php

namespace Mattoid\StoreInvite\Controller;

use Flarum\Api\Controller\AbstractListController;
use Flarum\Foundation\ValidationException;
use Flarum\Http\RequestUtil;
use Flarum\Http\UrlGenerator;
use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Exception\PermissionDeniedException;
use Flarum\User\UserRepository;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Contracts\Events\Dispatcher;
use Mattoid\StoreInvite\Helpers\CommonHelper;
use Mattoid\StoreInvite\Model\InviteModel;
use Mattoid\StoreInvite\Serializer\InviteSerializer;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class EditInviteConfirmController extends AbstractListController
{

    public $serializer = InviteSerializer::class;

    protected $translator;
    protected $settings;
    protected $events;
    protected $cache;


    public function __construct(SettingsRepositoryInterface $settings, UserRepository $repository, Dispatcher $events, UrlGenerator $url, Translator $translator, CacheContract $cache)
    {
        $this->url = $url;
        $this->cache = $cache;
        $this->events = $events;
        $this->settings = $settings;
        $this->translator = $translator;
        $this->repository = $repository;
    }

    protected function data(ServerRequestInterface $request, Document $document) {
        $actor = RequestUtil::getActor($request);
        $params = $request->getParsedBody();

        if (!$actor->can('mattoid-store-invite.group-admin-view')) {
            throw new PermissionDeniedException();
        }

        $invite = InviteModel::query()->where('id', $params['id'])->where('status', 0)->first();
        if (!$invite) {
            throw new ValidationException(['message' => $this->translator->trans('mattoid-store-invite.forum.error.invite-not-exist')]);
        }

        CommonHelper::confirm($actor, $params, $invite);
    }
}
