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

class OuttimeDeleteCommand extends AbstractCommand
{
    protected $cache;
    protected $events;
    protected $settings;
    protected $translator;

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
        $this->setName('mattoid:store:invite:delete:outtime')->setDescription('Invite Outtime Delete');
    }

    protected function fire()
    {
        $outtime = $this->settings->get('mattoid-store-invite.invite-validity-period', 3);
        $datetime = Carbon::now()->subDays($outtime)->tz($this->storeTimezone);
        $inviteList = InviteModel::query()->where('confirm_time','<=', $datetime)->where('status', 1)->where('is_expire', 0)->get();
        $this->info(json_encode($inviteList));
        // 没有超时的邀请码，直接跳过本次任务
        if (!$inviteList) {
            return;
        }

        foreach ($inviteList as $invite) {
            try {
                CommonHelper::delete($invite);
            } catch (\Exception $e) {
                $this->error($this->translator->trans('mattoid-store-invite.forum.error.exception', ['massage' => $e->getMessage()]));
            }
        }
    }
}
