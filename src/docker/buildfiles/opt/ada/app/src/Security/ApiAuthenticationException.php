<?php
declare(strict_types = 1);

namespace App\Security;

use App\Enum\GenerateTokenError;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class ApiAuthenticationException extends AuthenticationException
{
    public function __construct(
        public readonly GenerateTokenError $error,
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct(
            "generate_token.{$this->error->name}",
            $code,
            $previous
        );
    }
}
