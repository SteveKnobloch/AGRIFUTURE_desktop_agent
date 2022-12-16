<?php
declare(strict_types = 1);

namespace App\Service;

use App\EventSubscriber\CurrentLocale;

final class PortalUrl
{
    public function __construct(
        private readonly CurrentLocale $locale,
        private readonly string $defaultLocale,
    ) {}

    public function __invoke(?string $locale = null): string
    {
        $locale ??= $this->locale->currentLocale();
        $locale ??= $this->defaultLocale;

        return getenv(
            'ADA_PORTAL_' . strtoupper($locale)
        );
    }

    public function __toString(): string
    {
        return $this();
    }
}
