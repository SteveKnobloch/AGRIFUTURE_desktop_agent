<?php
declare(strict_types = 1);

namespace App\EventListener;

use App\Controller\ErrorController;
use App\Entity\Token;
use App\Enum\GetTokenInformationError;
use App\Repository\TokenRepository;
use App\Service\ApiService;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final class EarlyErrorDetection
{
    public function __construct(
        private readonly ApiService $api,
        private readonly TokenRepository $tokens,
    ) {}

    public function __invoke(RequestEvent $request): void
    {
        if (!$request->isMainRequest()) {
            // Only check once for the main request
            return;
        }

        $locale = $request->getRequest()->getLocale();

        // Check if the token is valid
        if ($this->tokens->current()) {
            $status = $this->api->getTokenInformation($locale);

            if ($status instanceof Token) {
                // We have internet and are logged in.
                return;
            }

            if ($status === GetTokenInformationError::InvalidToken) {
                $this->forwardToError($request, 'invalidToken');
                return;
            }

            if ($status === GetTokenInformationError::ApiAccessForbidden) {
                $this->forwardToError($request, 'apiAccessForbidden');
                return;
            }
        }

        // Check if the user is connected to the internet
        if (!$this->api->checkInternetConnectivity($locale)) {
            $this->forwardToError($request, 'offline');
            return;
        }
    }

    private function forwardToError(RequestEvent $request, string $method) {
        $request->getRequest()->attributes->set(
            '_controller',
            ErrorController::class . "::$method"
        );
    }
}
