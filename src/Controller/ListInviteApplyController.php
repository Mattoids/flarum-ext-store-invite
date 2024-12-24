<?php

namespace Mattoid\StoreInvite\Controller;

use Carbon\Carbon;
use Flarum\Api\Controller\AbstractListController;
use Flarum\Http\RequestUtil;
use Flarum\Http\UrlGenerator;
use Flarum\Locale\Translator;
use Flarum\User\UserRepository;
use Illuminate\Support\Arr;
use Flarum\Post\Post;
use Mattoid\StoreInvite\Model\InviteHistoryModel;
use Mattoid\StoreInvite\Model\InviteModel;
use Flarum\Settings\SettingsRepositoryInterface;
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

    public function __construct(SettingsRepositoryInterface $settings, UserRepository $repository, UrlGenerator $url, Translator $translator)
    {
        $this->url = $url;
        $this->translator = $translator;
        $this->repository = $repository;

        $storeTimezone = $settings->get('mattoid-store.storeTimezone', 'Asia/Shanghai');
        $this->storeTimezone = !!$storeTimezone ? $storeTimezone : 'Asia/Shanghai';
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
            if (!$actor->can('mattoid-store-invite.group-admin-view')) {
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
        })->skip($offset)
            ->take($limit + 1)
            ->orderBy("id", $status == 0 ? 'asc' : 'desc')
            ->get();

        if ($list) {
            $userIdList = [];
            foreach ($list as $item) {
                $userIdList[] = $item->user->id;
            }
            $inviteUserMap = [];
            $inviteUserList = InviteModel::query()->selectRaw("count(1) as totalNum, sum(IF(status = 1, 1, 0)) as passTotalNum, user_id")->whereIn('user_id', $userIdList)->groupBy('user_id')->get();
            foreach ($inviteUserList as $item) {
                $inviteUserMap[$item->user_id] = $item;
            }

            $postList = Post::query()->selectRaw("count(1) as postNum, user_id")->whereIn('user_id', $userIdList)->groupBy('user_id')->get();
            foreach ($postList as $item) {
                $postListMap[$item->user_id] = $item;
            }

            $inviteHistoryMap = [];
            $inviteHistory = InviteHistoryModel::query()->whereIn('user_id', $userIdList)->where("year", Carbon::now()->tz($this->storeTimezone)->year)->get();
            foreach ($inviteHistory as $item) {
                $inviteHistoryMap[$item->user_id] = $item;
            }

            $noteList = [];
            if (class_exists("\FoF\ModeratorNotes\Model\ModeratorNote")) {
                $result = \FoF\ModeratorNotes\Model\ModeratorNote::query()->whereIn('user_id', $userIdList)->selectRaw("count(1) as total, user_id")->groupBy('user_id')->get();
                foreach ($result as $note) {
                    $noteList[$note->user_id] = $note->total;
                }
            }

            foreach ($list as $item) {
                $item['postNum'] = $postListMap[$item->user_id]->postNum ?? 0;
                $item['userCreateTime'] = Carbon::parse($item->user->joined_at, $this->storeTimezone)->format('Y-m-d');
                if ($inviteUserMap[$item->user_id]) {
                    $item['totalNum'] = $inviteUserMap[$item->user_id]->totalNum;
                    $item['passTotalNum'] = $inviteUserMap[$item->user_id]->passTotalNum;
                }
                if ($inviteHistoryMap[$item->user_id]) {
                    $item['lastYearApply'] = $inviteHistoryMap[$item->user_id]->apply;
                    $item['lastYearPass'] = $inviteHistoryMap[$item->user_id]->pass;
                }
                if ($noteList[$item->user_id]) {
                    $item['notes'] = $noteList[$item->user_id];
                } else {
                    $item['notes'] = 0;
                }
            }
        }

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
