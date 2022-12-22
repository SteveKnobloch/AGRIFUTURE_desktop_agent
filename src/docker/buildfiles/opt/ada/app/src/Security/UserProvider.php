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
use function Symfony\Component\Translation\t;

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
    ): UserInterface {
        // We first use the token from the database, but fall back to the user
        // session. This allows changing to another account as well as using
        // browser sync to share a token between devices, but it also could
        // lead to an easy way of resuming a session if the user doesn’t
        // disconnect using the portal.

        $current = $this->tokens->current();
        if ($current) {
            return $current;
        }

        $user = $this->api->getTokenInformation(
            'de',
            $token
        );
        if ($user instanceof Token) {
            $this->tokens->save($user, true);
            return $user;
        }

        throw new UserNotFoundException();
    }
}
