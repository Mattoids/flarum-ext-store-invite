<?php

namespace Mattoid\StoreInvite\Listeners;

use Mattoid\Store\Event\StoreBuyEvent;
use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;

class StoreInviteListeners
{

    private $events;
    private $settings;

    public function __construct(Dispatcher $events, SettingsRepositoryInterface $settings, Translator $translator)
    {
        $this->events = $events;
        $this->settings = $settings;
    }

    public function handle(StoreBuyEvent $event) {
    }
}
