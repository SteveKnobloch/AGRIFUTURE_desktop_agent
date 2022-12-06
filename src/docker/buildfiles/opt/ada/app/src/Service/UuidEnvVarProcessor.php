<?php
declare(strict_types = 1);

namespace App\Service;

use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;
use Symfony\Component\Uid\Uuid;

final class UuidEnvVarProcessor implements EnvVarProcessorInterface
{
    public function getEnv(
        string $prefix,
        string $name,
        \Closure $getEnv
    ): mixed {
        return Uuid::fromString($getEnv($name));
    }

    public static function getProvidedTypes(): array
    {
        return [
            'uuid' => 'string',
        ];
    }
}
