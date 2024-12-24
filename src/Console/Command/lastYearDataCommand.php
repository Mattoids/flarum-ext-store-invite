<?php

namespace Mattoid\StoreInvite\Console\Command;

use Carbon\Carbon;
use Flarum\Console\AbstractCommand;
use Mattoid\StoreInvite\Model\InviteHistoryModel;
use Mattoid\StoreInvite\Model\InviteModel;
use Symfony\Contracts\Translation\TranslatorInterface;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Cache\Repository;

class lastYearDataCommand extends AbstractCommand
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
        $this->setName('mattoid:store:invite:lastYear:data')->setDescription('Last year\'s data');
    }

    protected function fire()
    {
        $year = Carbon::now()->tz($this->storeTimezone)->subYear()->year;
        $startTime = Carbon::now()->tz($this->storeTimezone)->subYear()->firstOfYear();
        $endTime = Carbon::now()->tz($this->storeTimezone)->subYear()->endOfYear();
        $inviteList = InviteModel::query()->selectRaw("user_id, count(1) as total, SUM(IF(status = 1, 1, 0)) as pass")->where('confirm_time', '>=', $startTime)->where('confirm_time','<=', $endTime)->groupBy("user_id")->get();
        if (!$inviteList || count($inviteList) == 0) {
            return;
        }

        $inviteData = [];
        foreach ($inviteList as $invite) {
            $inviteData['user_id'] = $invite->user_id;
            $inviteData['year'] = $year;
            $inviteData['apply'] = $invite->total;
            $inviteData['pass'] = $invite->pass;
        }

        InviteHistoryModel::query()->where('year', $year)->delete();
        InviteHistoryModel::query()->insert($inviteData);
    }
}
