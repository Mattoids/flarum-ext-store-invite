<?php

namespace Mattoid\StoreInvite\Console;

use Flarum\Foundation\Paths;
use Illuminate\Console\Scheduling\Event;
use Flarum\Settings\SettingsRepositoryInterface;

class PublishSchedule
{
    public function __invoke(Event $event) {
        $settings = resolve(SettingsRepositoryInterface::class);
        $storeTimezone = $settings->get('mattoid-store.storeTimezone', 'Asia/Shanghai');
        $settingTimezone = !!$storeTimezone ? $storeTimezone : 'Asia/Shanghai';

        // 设置时间
        $event->everyFiveMinutes()->withoutOverlapping()->timezone($settingTimezone);
//        $event->everyMinute()->withoutOverlapping()->timezone($settingTimezone);

        $paths = resolve(Paths::class);
        $event->appendOutputTo($paths->storage.(DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'mattoid-store-invite.log'));
    }
}
