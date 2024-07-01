<?php

namespace Mattoid\StoreInvite\Listeners;

use Flarum\Foundation\ValidationException;
use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Illuminate\Contracts\Events\Dispatcher;
use Mattoid\StoreInvite\Event\InviteEvent;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Mattoid\StoreInvite\Model\InviteModel;

class InviteListeners
{

    private $cache;
    private $events;
    private $settings;
    private $translator;

    public function __construct(Dispatcher $events, SettingsRepositoryInterface $settings, Translator $translator, CacheContract $cache)
    {
        $this->cache = $cache;
        $this->events = $events;
        $this->settings = $settings;
        $this->translator = $translator;
    }

    public function handle(InviteEvent $event) {
        $user = $event->user;
        $invite = $event->invite;
        $key = md5($invite->email + $invite->user_id);
        // 受邀人是否存在
        $user = User::query()->where('email', $invite->email)->orWhere('second_email', $invite->email)->first();
        if ($user) {
            throw new ValidationException(['message' => $this->translator->trans('mattoid-store-invite.forum.error.email-exist')]);
        }

        $this->sendInvites($invite, $invite->invite_code);

        $this->cache->delete($key);
    }

    private function sendInvites(InviteModel $invite, $inviteCode)
    {
        $replace = [
            '[user]' => $invite->user->username,
            '[code]' => $inviteCode,
        ];

        $body = $this->settings->get('mattoid-store-invite.mail');

        foreach ($replace as $key => $value) {
            $body = str_ireplace($key, $value, $body);
        }

        // 发送验证码
        $this->mailer->raw($body, function (Message $message) use ($invite) {
            $message->to($invite->email);
            $message->subject($this->settings->get('mattoid-store-invite.mail.title', $this->translator->trans('mattoid-store-invite.forum.mail.send-invite-title')));
        });
    }
}
