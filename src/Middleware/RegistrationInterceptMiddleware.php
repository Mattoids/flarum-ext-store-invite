<?php

namespace Mattoid\StoreInvite\Middleware;

use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Illuminate\Support\Arr;
use Mattoid\StoreInvite\Model\InviteModel;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Flarum\Foundation\ValidationException;

class RegistrationInterceptMiddleware implements MiddlewareInterface
{

    private $settings;
    private $translator;

    public function __construct(Translator $translator, SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
        $this->translator = $translator;
    }
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $email = Arr::get($request->getParsedBody(), 'data.attributes.email');
        $username = Arr::get($request->getParsedBody(), 'data.attributes.username');
        $inviteCode = Arr::get($request->getParsedBody(), 'data.attributes.fof-doorkey');

        $invite = InviteModel::query()->where('invite_code', $inviteCode)->orderByDesc('id')->first();
        if ($invite) {
            if ($invite->email != $email && $this->settings->get('mattoid-store-invite.inconsistency.email', 0)) {
                throw new ValidationException(['message' => $this->translator->trans('mattoid-second-email.forum.error.email-inconsistency')]);
            }
            if ($invite->username != $username && $this->settings->get('mattoid-store-invite.inconsistency.username', 0)) {
                throw new ValidationException(['message' => $this->translator->trans('mattoid-second-email.forum.error.username-inconsistency')]);
            }
        }

        return $handler->handle($request);
    }
}
