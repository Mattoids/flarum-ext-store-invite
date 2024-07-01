<?php

namespace Mattoid\StoreInvite\Controller;

use Carbon\Carbon;
use Flarum\Api\Controller\AbstractListController;
use Flarum\Foundation\ValidationException;
use Flarum\Http\RequestUtil;
use Flarum\Http\UrlGenerator;
use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Exception\PermissionDeniedException;
use Flarum\User\User;
use Flarum\User\UserRepository;
use Illuminate\Contracts\Events\Dispatcher;
use Mattoid\StoreInvite\Event\InviteEvent;
use Mattoid\StoreInvite\Model\InviteModel;
use Mattoid\StoreInvite\Serializer\InviteSerializer;
use Mattoid\StoreInvite\Utils\StringUtil;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Illuminate\Contracts\Cache\Repository as CacheContract;

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

        // 审核通过
        if ($params['status'] == 1) {
            $invite->invite_code = StringUtil::getInviteCode($invite->user_id);
            $key = md5($invite->email + $invite->user_id);
            if (!$this->cache->add($key, $invite->invite_code, 5)) {
                throw new ValidationException(['message' => $this->translator->trans('mattoid-store-invite.forum.error.lock-resources')]);
            }

            // 扣费
            $user = User::query()->where('id', $actor->id)->first();
            $price = $this->settings->get('mattoid-store-invite.price', 0);
            $money = $user->money;
            $balance = $money - $price;
            if ($balance < 0) {
                throw new ValidationException(['message' => $this->translator->trans('mattoid-store-invite.forum.error.user-balance-low')]);
            }

            $user->money = $balance;
            $user->where('money', $money);
            if (!$user->save()) {
                throw new ValidationException(['message' => $this->translator->trans('mattoid-store-invite.forum.error.user-balance-low')]);
            }

            // 发送邀请码
            $this->events->dispatch(new InviteEvent($actor, $invite));
        }

        $invite->confirm_remark = $params['confirmRemark'];
        $invite->status = $params['status'];
        $invite->confirm_time = Carbon::now()->tz($this->settings->get('mattoid-store.storeTimezone', 'Asia/Shanghai'));
        $invite->updated_at = Carbon::now()->tz($this->settings->get('mattoid-store.storeTimezone', 'Asia/Shanghai'));
        $invite->save();
    }
}
