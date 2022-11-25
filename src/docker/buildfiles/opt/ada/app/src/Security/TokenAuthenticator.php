<?php
declare(strict_types = 1);

namespace App\Security;

use App\Entity\Token;
use App\Repository\TokenRepository;
use App\Service\ApiService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class TokenAuthenticator extends AbstractAuthenticator
{
    private readonly ?Token $token;

    public function __construct(
        private TokenRepository $tokens,
        private readonly ApiService $api,
        private readonly UrlGeneratorInterface $routes,
    ) {
        $this->token = $tokens->current();
    }

    public function supports(Request $request): ?bool
    {
        return $this->token !== null;
    }

    public function authenticate(Request $request): Passport
    {
        return new SelfValidatingPassport(
            new UserBadge(
                $this->token->token,
                fn() => $this->token
            ),
        );
    }

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName
    ): ?Response {
        return null;
    }

    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception
    ): ?Response {
        $this->tokens->logout();

        return new RedirectResponse(
            $this->routes->generate('app_page_user_account_connect'),
            303
        );
    }
}
