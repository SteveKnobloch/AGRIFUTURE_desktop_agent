<?php
declare(strict_types = 1);

namespace App\Service;

use App\Entity\Analysis;
use App\Entity\Token;
use App\Enum\AnalysisType;
use App\Enum\CreateAnalysisError;
use App\Enum\FileFormat;
use App\Enum\GenerateTokenError;
use App\Enum\GetTokenInformationError;
use App\Form\Entity\AnalysisInput;
use Symfony\Component\HttpClient\Exception\JsonException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ApiService
{
    private readonly array $globalOptions;

    public function __construct(
        private readonly string $apiPrefix,
        private readonly HttpClientInterface $http,
        private readonly Security $security,
        $checkCertificates = true,
        private readonly Uuid $uuid,
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

    public function createAnalysis(
        string $locale,
        AnalysisInput $analysis
    ): Analysis|CreateAnalysisError {
        $array = [
            'type' => match ($analysis->getType()) {
                AnalysisType::pathogens => [
                    'type' => AnalysisType::pathogens->value,
                    'subSpeciesLevel' => $analysis->isSubSpeciesLevel(),
                    'sensitiveMode' => $analysis->isSensitiveMode(),
                ],
                default => [
                    'type' => $analysis->getType()->value,
                ]
            },
            'format' => match ($analysis->getFormat()) {
                FileFormat::fast5 => [
                    'type' => FileFormat::fast5->value,
                    'flowcellType' => $analysis->getFlowcellType(),
                    'libraryToolkit' => $analysis->getLibraryToolkit(),
                ],
                default => [
                    'type' => $analysis->getFormat()->value,
                ]
            },
            'name' => $analysis->getName(),
            'location' => [
                'country' => $analysis->getCountry(),
            ],
            'minQualityScore' => $analysis->getMinQualityScore(),
            'minSequenceLength' => $analysis->getMinSequenceLength(),
        ];

        if ($analysis->getHost()) {
            $array['host'] = $analysis->getHost();
        }
        if ($analysis->getCity()) {
            $array['location']['city'] = $analysis->getCity();
        }
        if ($analysis->getCoordinates()->getLongitude() !== null) {
            $array['location']['longitude'] =
                $analysis->getCoordinates()->getLongitude();
        }
        if ($analysis->getCoordinates()->getLatitude() !== null) {
            $array['location']['latitude'] =
                $analysis->getCoordinates()->getLatitude();
        }

        $json = json_encode($array, flags: JSON_THROW_ON_ERROR);

        try {
            $response = $this->http->request(
                'PUT',
                $this->url($locale, 'analysis'),
                [
                    'body' => $json,
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'X-Api-Key' => $this->token(),
                    ],
                    ...$this->globalOptions
                ]
            );
        } catch (TransportExceptionInterface $e) {
            return CreateAnalysisError::UnknownError;
        }

        try {
            $status = $response->getStatusCode();
        } catch (TransportExceptionInterface $e) {
            return CreateAnalysisError::UnknownError;
        }

        switch($status) {
            case 201:
                try {
                    $json = json_decode(
                        $response->getContent(false),
                        flags: JSON_THROW_ON_ERROR
                    );
                } catch (ExceptionInterface|JsonException $e) {
                    return CreateAnalysisError::UnknownError;
                }

                return new Analysis(
                    $this->uuid,
                    $json->id,
                    $json->name,
                    false,
                    $analysis->getDirectory(),
                    $analysis->getFormat(),
                );
            case 401:
                return CreateAnalysisError::InvalidToken;
            case 403:
                return CreateAnalysisError::ApiAccessForbidden;
            default:
                return CreateAnalysisError::UnknownError;
        }
    }

    public function checkInternetConnectivity(
        string $locale,
    ): bool {
        try {
            $response = $this->http->request(
                'GET',
                $this->url($locale, 'ping'),
                $this->globalOptions,
            );

            return $response->getStatusCode() === 204;
        } catch (ExceptionInterface $e) {
            return false;
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
