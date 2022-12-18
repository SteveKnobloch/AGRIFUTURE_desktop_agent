<?php
declare(strict_types = 1);

namespace App\EventListener;

use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class DetectLanguage
{
    public function __invoke(ExceptionEvent $event): void
    {
        if (!$event->getThrowable() instanceof NotFoundHttpException) {
            return;
        }

        $locales = ['en', 'de'];
        foreach ($locales as $locale) {
            if (str_starts_with(
                $event->getRequest()->getPathInfo(),
                "/$locale/"
            )) {
                return;
            }
        }

        foreach ($event->getRequest()->getLanguages() as $requested) {
            $requested = strtolower(substr($requested, 0, 2));
            if (in_array($requested, $locales)) {
                $event->setResponse(
                    new RedirectResponse(
                        "/$requested{$event->getRequest()->getPathInfo()}"
                    )
                );
            }
        }
    }
}
