<?php

namespace Mattoid\StoreInvite\Console\Command;

use Carbon\Carbon;
use Flarum\Console\AbstractCommand;
use Flarum\Foundation\ValidationException;
use Mattoid\StoreInvite\Helpers\CommonHelper;
use Mattoid\StoreInvite\Model\InviteModel;
use Symfony\Contracts\Translation\TranslatorInterface;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Cache\Repository;
use Mattoid\Store\Event\StoreInvalidEvent;
use Mattoid\Store\Extend\StoreExtend;
use Mattoid\Store\Model\StoreCartModel;
use Mattoid\Store\Model\StoreModel;

class AutoReviewCommand extends AbstractCommand
{
    protected $events;
    protected $settings;

    private $storeTimezone = 'Asia/Shanghai';

    public function __construct(SettingsRepositoryInterface $settings, TranslatorInterface $translator, Repository $cache, Dispatcher $events) {
        parent::__construct();
        $this->cache = $cache;
        $this->events = $events;
        $this->settings = $settings;
        $this->translator = $translator;

        $storeTimezone = $this->settings->get('mattoid-store.storeTimezone', 'Asia/Shanghai');
        $this->storeTimezone = !!$storeTimezone ? $storeTimezone : 'Asia/Shanghai';
    }

    protected function configure()
    {
        $this->setName('mattoid:store:invite:auto:review')->setDescription('Invite Auto Review');
    }

    protected function fire()
    {
        $autoReview = $this->settings->get('mattoid-store-invite.auto.review', 0);
        if (!$autoReview) {
            return;
        }

        // 为防止数据过大导致查询过慢的问题，这里只查询一天的数据
        $datetime = Carbon::now()->subDay()->tz($this->storeTimezone);
        $inviteList = InviteModel::query()->where('created_at','>=', $datetime)->where('status', 0)->get();
        // 没有待审核数据，直接无视
        if (!$inviteList) {
            return;
        }

        $params= [
            'status' => 1,
            'confirmRemark' => $this->translator->trans('mattoid-store-invite.forum.system-review')
        ];

        $user = User::query()->where('username', $this->settings->get('mattoid-store-invite.auto.review.username', 'admin'))->first();
        foreach ($inviteList as $invite) {
            try {
                CommonHelper::confirm($user, $params, $invite);
            } catch (\Exception $e) {
                $this->error($this->translator->trans('mattoid-store-invite.forum.error.exception', ['massage' => $e->getMessage()]));
            }
        }
    }
}
