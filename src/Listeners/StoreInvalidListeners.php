<?php

namespace Mattoid\StoreInvite\Listeners;

use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Mattoid\Store\Event\StoreInvalidEvent;

class StoreInvalidListeners
{
    private $events;
    private $settings;

    public function __construct(Dispatcher $events, SettingsRepositoryInterface $settings, Translator $translator)
    {
        $this->events = $events;
        $this->settings = $settings;
    }

    public function handle(StoreInvalidEvent $event) {
        app('log')->info('商品失效事件');
    }
}
