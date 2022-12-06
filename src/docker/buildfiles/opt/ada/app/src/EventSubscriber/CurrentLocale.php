<?php
declare(strict_types = 1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class CurrentLocale implements EventSubscriberInterface
{
    private ?string $locale = null;

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => '__invoke'
        ];
    }

    public function __invoke(RequestEvent $requestEvent)
    {
        $this->locale = $requestEvent->getRequest()->getLocale();
    }

    public function currentLocale(): ?string
    {
        return $this->locale;
    }
}
