<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Entity\Token;
use App\Enum\GenerateTokenError;
use App\Form\Entity\Login;
use App\Form\LoginForm;
use App\Repository\TokenRepository;
use App\Security\ApiAuthenticationException;
use App\Service\ApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserAccountController extends AbstractController
{
    #[Route(
        path: '/{_locale}/user_account/connect',
        name: 'app_page_user_account_connect',
        requirements: [
            '_locale' => 'en|de',
        ],
    )]
    public function user_account_connect(
        Request $request,
        AuthenticationUtils $auth,
        TranslatorInterface $i18n,
        TokenRepository $tokens,
    ): Response {
        if ($tokens->current()) {
            return $this->redirectToRoute('app_page_user_account_show');
        }

        $form = $this->createForm(
            LoginForm::class,
            new Login(
                username: $auth->getLastUsername(),
            )
        );

        $form->handleRequest($request);

        $error = $auth->getLastAuthenticationError();
        /** @var GenerateTokenError|null $reason */
        $reason = null;
        if ($error instanceof ApiAuthenticationException) {
            $reason = $error->error;
        }

        if ($error) {
            $form->get(
                match($reason) {
                    GenerateTokenError::NameAlreadyExists => 'name',
                    GenerateTokenError::ApiAccessForbidden =>
                      'username',
                    default => 'password'
                }
            )->addError(
                new FormError(
                    $i18n->trans(
                        match ($reason) {
                            GenerateTokenError::InvalidUsernameOrPassword =>
                                'Invalid credentials.',
                            GenerateTokenError::UnknownError =>
                                'Authentication request could not be processed due to a system problem.',

                            default => $error->getMessage(),
                        },
                        domain: 'security'
                    ),
                )
            );
        }

        return $this->render(
            'pages/userAccount/connect.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    #[Route(
        path: '/{_locale}/user_account',
        name: 'app_page_user_account_show',
        requirements: [
            '_locale' => 'en|de',
        ],
    )]
    public function user_account_view(): Response
    {
        return $this->render('pages/userAccount/show.html.twig');
    }
}
