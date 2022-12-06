<?php
declare(strict_types = 1);

namespace App\EventSubscriber;

use App\Repository\TokenRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TokenRepository $tokens,
    ) {}

    public static function getSubscribedEvents()
    {
        return[LogoutEvent::class => 'onLogout'];
    }

    public function onLogout(LogoutEvent $logout): void
    {
        $this->tokens->logout(flush: true);
    }
}
