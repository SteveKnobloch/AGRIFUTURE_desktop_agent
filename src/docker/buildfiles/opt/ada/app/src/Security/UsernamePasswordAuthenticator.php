<?php
declare(strict_types = 1);

namespace App\Security;

use App\Entity\Token;
use App\Enum\GenerateTokenError;
use App\Form\LoginForm;
use App\Repository\TokenRepository;
use App\Service\ApiService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class UsernamePasswordAuthenticator extends AbstractAuthenticator
{
    private readonly FormInterface $form;
    private Token|GenerateTokenError $user;

    public function __construct(
        FormFactoryInterface $formFactory,
        private readonly ApiService $api,
        private readonly TokenRepository $tokens,
        private readonly UrlGeneratorInterface $routes,
    ) {
        $this->form = $formFactory->create(
            LoginForm::class
        );
    }

    public function supports(Request $request): ?bool
    {
        if ($this->form->isEmpty()) {
            $this->form->handleRequest($request);
        }
        return $this->form->isSubmitted() && $this->form->isValid();
    }

    public function authenticate(Request $request): Passport
    {
        if ($this->form->isEmpty()) {
            $this->form->handleRequest($request);
        }

        /** @var \App\Form\Entity\Login $login */
        $login = $this->form->getData();

        $this->user = $this->api->generateToken(
            $request->getLocale(),
            $login->getName(),
            $login->getUsername(),
            $login->getPassword(),
        );

        $valid = $this->user instanceof Token;

        $id = $valid ?
            $this->user->token :
            $login->getUsername();

        $dummyUser = new Token('', '', '');

        return new Passport(
            new UserBadge(
                $id,
                fn() => $valid ? $this->user : $dummyUser,
            ),
            new CustomCredentials(
                function () use ($valid) {
                    if (!$valid) {
                        throw new ApiAuthenticationException($this->user);
                    }

                    return true;
                },
                $id
            )
        );
    }

    /**
     * @param Token $token
     */
    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName
    ): ?Response {
        $this->tokens->save($this->user, true);
        return new RedirectResponse(
            $this->routes->generate('app_page_user_account_show'),
            303
        );
    }

    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception
    ): ?Response {
        $request->attributes->set(
            Security::AUTHENTICATION_ERROR,
            $exception
        );
        return null;
    }
}
