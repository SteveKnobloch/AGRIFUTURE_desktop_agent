<?php
declare(strict_types = 1);

namespace App\Service;

use App\Entity\Token;
use App\Enum\GenerateTokenError;
use App\Enum\GetTokenInformationError;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiService
{
    private readonly array $globalOptions;

    public function __construct(
        private readonly string $apiPrefix,
        private readonly HttpClientInterface $http,
        private readonly Security $security,
        $checkCertificates = true,
    ) {
        $this->globalOptions = $checkCertificates ? [] :
            [
                'verify_peer' => false,
                'verify_host' => false,
            ];
    }

    public function generateToken(
        string $locale,
        string $name,
        string $username,
        string $password
    ): Token|GenerateTokenError {
        $json = json_encode([
            'name' => $name,
            'username' => $username,
            'password' => $password,
        ]);

        try {
            $response = $this->http->request(
                'POST',
                $this->url($locale, 'login'),
                [
                    'body' => $json,
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ],
                    ...$this->globalOptions
                ]
            );
        } catch (TransportExceptionInterface $e) {
            return GenerateTokenError::UnknownError;
        }

        try {
            $status = $response->getStatusCode();
        } catch (TransportExceptionInterface $e) {
            return GenerateTokenError::UnknownError;
        }


        switch ($status) {
            case 200:
                try {
                    $token = $response->getContent(false);
                } catch (ExceptionInterface) {
                    return GenerateTokenError::UnknownError;
                }

                return new Token(
                    $token,
                    $name,
                    $username,
                );
            case 401:
                return GenerateTokenError::InvalidUsernameOrPassword;
            case 403:
                return GenerateTokenError::ApiAccessForbidden;
            case 409:
                return GenerateTokenError::NameAlreadyExists;
            default:
                return GenerateTokenError::UnknownError;
        }
    }

    public function getTokenInformation(
        string $locale,
        string $token = null
    ): Token|GetTokenInformationError
    {
        $token ??= $this->token();
        if (!$token) {
            return GetTokenInformationError::InvalidToken;
        }

        try {
            $response = $this->http->request(
                'GET',
                $this->url($locale, 'login'),
                [
                    'headers' => [
                        'X-Api-Key' => $token
                    ],
                    ...$this->globalOptions
                ]
            );
        } catch (TransportExceptionInterface) {
            return GetTokenInformationError::UnknownError;
        }

        try {
            $status = $response->getStatusCode();
        } catch (TransportExceptionInterface) {
            return GetTokenInformationError::UnknownError;
        }

        switch ($status) {
            case 200:
                try {
                    $content = json_decode(
                        $response->getContent(false),
                        flags: JSON_THROW_ON_ERROR
                    );
                } catch (\JsonException|ExceptionInterface) {
                    return GetTokenInformationError::UnknownError;
                }

                return new Token(
                    $token,
                    $content->name,
                    $content->username,
                );
            case 401:
                return GetTokenInformationError::InvalidToken;
            case 403:
                return GetTokenInformationError::ApiAccessForbidden;
            default:
                return GetTokenInformationError::UnknownError;
        }
    }

    private function token(): ?string
    {
        /** @var null|User $user */
        $user = $this->security->getUser();
        return $user?->token;
    }

    private function url(string $locale, string $path)
    {
        $localePath = match ($locale) {
            'de' => '',
            default => "$locale/"
        };

        return "{$this->apiPrefix}/{$localePath}api/$path";
    }
}
