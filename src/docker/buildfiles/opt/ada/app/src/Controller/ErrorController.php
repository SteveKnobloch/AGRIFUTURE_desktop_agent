<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Service\ApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ErrorController extends AbstractController
{
    #[Route(
        path: '/{_locale}/error/invalid-token',
        name: 'app_error_invalid_token',
        requirements: [
            '_locale' => 'en|de',
        ],
    )]
    public function invalidToken(
        ApiService $apiService,
        string $_locale,
    ): Response
    {
        if (!$apiService->checkInternetConnectivity($_locale)) {
            $this->forward(self::class . '::offline');
        }

        // ToDo This should be a nice view
        return $this->json('Your token is invalid, log in again.');
    }

    #[Route(
        path: '/{_locale}/error/api-access-forbidden',
        name: 'app_error_api_access_forbidden',
        requirements: [
            '_locale' => 'en|de',
        ],
    )]
    public function apiAccessForbidden(
        ApiService $apiService,
        string $_locale,
    ): Response {
        if (!$apiService->checkInternetConnectivity($_locale)) {
            $this->forward(self::class . '::offline');
        }

        // ToDo This should be a nice view
        return $this->json('You can’t use the API, log in again.');
    }

    #[Route(
        path: '/{_locale}/error/offline',
        name: 'app_error_offline',
        requirements: [
            '_locale' => 'en|de',
        ],
    )]
    public function offline(): Response
    {
        // ToDo This should be a nice view
        return $this->json(
            'Cannot connect to the portal. Are you offline?'
        );
    }
}
