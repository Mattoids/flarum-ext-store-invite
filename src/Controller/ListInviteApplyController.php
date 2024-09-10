<?php

namespace Mattoid\StoreInvite\Controller;

use Flarum\Api\Controller\AbstractListController;
use Flarum\Http\RequestUtil;
use Flarum\Http\UrlGenerator;
use Flarum\Locale\Translator;
use Flarum\User\UserRepository;
use Illuminate\Support\Arr;
use Mattoid\StoreInvite\Model\InviteModel;
use Mattoid\StoreInvite\Serializer\InviteSerializer;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Flarum\User\User;

class ListInviteApplyController extends AbstractListController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = InviteSerializer::class;

    public function __construct(UserRepository $repository, UrlGenerator $url, Translator $translator)
    {
        $this->url = $url;
        $this->translator = $translator;
        $this->repository = $repository;
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $params = $request->getQueryParams();
        $limit = $this->extractLimit($request);
        $offset = $this->extractOffset($request);
        $status = Arr::get($params, 'filter.status');
        $query = Arr::get($params, 'filter.query');
        $username = Arr::get($params, 'filter.username');

        $queryUser = User::query()->where('username', $username)->first();
        $list = InviteModel::query()->where(function($where) use ($actor, $query, $status, $queryUser) {
            if (!$actor->can('mattoid-store-invite.admin-group')) {
                $where->where('user_id', $actor->id);
            }
            if ($queryUser) {
                $where->where('user_id', $queryUser->id);
            }
            if ($status >= 0) {
                $where->where('status', $status);
            }
            if ($query) {
                $userIdList = [];
                $userList = User::query()->where('username', 'like', "{$query}%")->get('id');
                foreach ($userList as $user) {
                    $userIdList[] = $user->id;
                }
                $where->where(function($where) use ($userIdList, $query) {
                    $where->whereIn('user_id', $userIdList)->orWhere('email', 'like', "{$query}%")->orWhere('username', 'like', "{$query}%");
                });
            }
        })->orderByDesc('id')->get();


        $results = $limit > 0 && $list->count() > $limit;
        if ($results) {
            $list->pop();
        }
        $document->addPaginationLinks(
            $this->url->to('api')->route('store.list'),
            $params,
            $offset,
            $limit,
            $results ? null : 0
        );

        return $list;
    }

}
