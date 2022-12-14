<?php
declare(strict_types = 1);

namespace App\Security;

use App\Entity\Token;
use App\Enum\GetTokenInformationError;
use App\Repository\TokenRepository;
use App\Service\ApiService;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    public function __construct(
        private readonly ApiService $api,
        private readonly TokenRepository $tokens,
    ) {}

    public function refreshUser(UserInterface $user)
    {
        return $this->loadFromApi(
            $user->getUserIdentifier(),
            $user,
        );
    }

    public function supportsClass(string $class)
    {
        return $class === Token::class;
    }

    public function loadUserByIdentifier(string $token): UserInterface
    {
        return $this->tokens->current() ??
            $this->loadFromApi($token);
    }

    private function loadFromApi(
        string $token,
        ?UserInterface $fallback = null,
    ): UserInterface {
        $user = $this->api->getTokenInformation(
            'de',
            $token
        );
        if ($user instanceof Token) {
            $this->tokens->save($user, true);
            return $user;
        }

        return $fallback ?: throw match($user) {
            GetTokenInformationError::InvalidToken,
            GetTokenInformationError::ApiAccessForbidden =>
            new UserNotFoundException(),
            GetTokenInformationError::UnknownError =>
            new \RuntimeException('Fetching token failed.')
        };
    }
}
