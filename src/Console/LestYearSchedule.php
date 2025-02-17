<?php

namespace Mattoid\StoreInvite\Console;

use Flarum\Foundation\Paths;
use Illuminate\Console\Scheduling\Event;
use Flarum\Settings\SettingsRepositoryInterface;

class LestYearSchedule
{
    public function __invoke(Event $event) {
        $settings = resolve(SettingsRepositoryInterface::class);
        $storeTimezone = $settings->get('mattoid-store.storeTimezone', 'Asia/Shanghai');
        $settingTimezone = !!$storeTimezone ? $storeTimezone : 'Asia/Shanghai';

        // 设置时间
        $event->cron('20 5 1 1 *')->withoutOverlapping()->timezone($settingTimezone);

        $paths = resolve(Paths::class);
        $event->appendOutputTo($paths->storage.(DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'mattoid-store-invite.log'));
    }
}
