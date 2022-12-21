<?php
declare(strict_types = 1);

namespace App\Service;

use App\Entity\Analysis;
use App\Entity\RemoteAnalysis;
use App\Enum\AnalysisStatus;
use App\Enum\AnalysisType;
use App\Enum\FileFormat;
use App\Enum\GetAnalysisError;
use App\EventSubscriber\CurrentLocale;
use App\Repository\AnalysisRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * An accessor for the current instance of the analysis as returned by the
 * server.
 *
 * The instance is loaded lazily as soon as fields are accessed which aren't
 * stored in the database are accessed. Database fields are updated as soon
 * as an instance is fetched.
 *
 * @api
 */
final class CurrentAnalysisFactory
{
    private RemoteAnalysis|GetAnalysisError|null $proxy;
    private RemoteAnalysis|GetAnalysisError|null $instance = null;
    private readonly Analysis|null $analysis;

    public function __construct(
        private readonly ApiService $api,
        private readonly AnalysisRepository $analyses,
        private readonly ManagerRegistry $doctrine,
        private readonly CacheInterface $remoteAnalysesCache,
        private readonly Uuid $uuid,
        private readonly CurrentLocale $locale,
    ) {
        $this->analysis = $this->analyses->current();

        $this->proxy = $this->analysis ?
            $this->buildProxy() :
            null;
    }

    /**
     * Gets the instance of the analysis.
     * On error, null is returned.
     * @api
     */
    public function __invoke(): RemoteAnalysis|null
    {
        return $this->proxy;
    }

    /**
     * Force-Fetch the analysis.
     * May returns the error produced.
     */
    public function forceFetch(): RemoteAnalysis|GetAnalysisError|null
    {
        return $this->fetch();
    }

    public function isRegistered(): bool
    {
        return $this->analysis !== null;
    }

    public function cached(): ?Analysis
    {
        return $this->analysis;
    }

    private function fetch(): RemoteAnalysis|GetAnalysisError|null
    {
        if ($this->instance !== null || $this->analysis === null) {
            return $this->instance;
        }

        /** @var \Symfony\Contracts\Cache\CacheInterface $pool */
        $analysis = $this->remoteAnalysesCache->get(
            $this->uuid->toBase58(),
            function () {
                return $this->api->getAnalysisStatus(
                    $this->locale->currentLocale() ?? 'en',
                    $this->analysis
                );
            }
        );

        if ($analysis instanceof RemoteAnalysis &&
            $this->analysis->getStatus() !== $analysis->getStatus() &&
            $analysis->getStatus() !== AnalysisStatus::unknown
        ) {
            $this->analysis->setStatus($analysis->getStatus());
            $this->analyses->save($this->analysis, true);
        }

        $this->instance = $analysis;
        if ($analysis instanceof RemoteAnalysis) {
            $this->proxy = $analysis;
        } else {
            $this->analysis->setStatus(AnalysisStatus::unknown);
            $this->analyses->save($this->analysis, true);
            $this->remoteAnalysesCache->delete($this->uuid->toBase58());
        }

        return $analysis;
    }

    private function buildProxy(): RemoteAnalysis|GetAnalysisError
    {
        if ($this->instance !== null || $this->analysis === null) {
            return $this->instance;
        }

        return new class(
            $this->analysis,
            self::fetch(...),
        ) extends RemoteAnalysis {
            private RemoteAnalysis|GetAnalysisError|null $real = null;

            public function __construct(
                private readonly Analysis $analysis,
                private readonly mixed $fetch,
            ) {}

            public function getId(): ?int
            {
                return $this->defferOrCached(
                    fn(RemoteAnalysis $a) => $a->getId(),
                    $this->analysis->remotePipelineId
                );
            }

            public function getType(): ?AnalysisType
            {
                return $this->deffer(
                    fn(RemoteAnalysis $a) => $a->getType(),
                );
            }

            public function getSubSpeciesLevel(): ?bool
            {
                return $this->deffer(
                    fn(RemoteAnalysis $a) => $a->getSubspeciesLevel(),
                );
            }

            public function getSensitiveMode(): ?bool
            {
                return $this->deffer(
                    fn(RemoteAnalysis $a) => $a->getSensitiveMode(),
                );
            }

            public function getStatus(): ?AnalysisStatus
            {
                return $this->defferOrCached(
                    fn(RemoteAnalysis $a) => $a->getStatus(),
                    $this->analysis->getStatus()
                );
            }

            public function getCreated(): ?\DateTimeInterface
            {
                return $this->deffer(
                    fn(RemoteAnalysis $a) => $a->getCreated()
                );
            }

            public function getLastReport(): ?\DateTimeInterface
            {
                return $this->deffer(
                    fn(RemoteAnalysis $a) => $a->getLastReport()
                );
            }

            public function getRunUntil(): ?\DateTimeInterface
            {
                return $this->deffer(
                    fn(RemoteAnalysis $a) => $a->getRunUntil()
                );
            }

            public function getFinishedTime(): ?\DateTimeImmutable
            {
                return $this->deffer(
                    fn(RemoteAnalysis $a) => $a->getFinishedTime()
                );
            }

            public function getFinishedReason(): ?string
            {
                return $this->deffer(
                    fn(RemoteAnalysis $a) => $a->getFinishedReason()
                );
            }

            public function getUploaded(): ?iterable
            {
                return $this->deffer(
                    fn(RemoteAnalysis $a) => $a->getUploaded()
                );
            }

            public function getFormat(): ?FileFormat
            {
                return $this->defferOrCached(
                    fn(RemoteAnalysis $a) => $a->getFormat(),
                    $this->analysis->fileType,
                );
            }

            public function getFlowcellType(): ?string
            {
                return $this->deffer(
                    fn(RemoteAnalysis $a) => $a->getFlowcellType()
                );
            }

            public function getLibraryToolkit(): ?string
            {
                return $this->deffer(
                    fn(RemoteAnalysis $a) => $a->getLibraryToolkit(),
                );
            }

            public function getName(): ?string
            {
                return $this->defferOrCached(
                    fn(RemoteAnalysis $a) => $a->getName(),
                    $this->analysis->name,
                );
            }

            public function getLatitude(): ?float
            {
                return $this->deffer(
                    fn(RemoteAnalysis $a) => $a->getLatitude(),
                );
            }

            public function getLongitude(): ?float
            {
                return $this->deffer(
                    fn(RemoteAnalysis $a) => $a->getLongitude()
                );
            }

            public function getCountry(): ?string
            {
                return $this->deffer(
                    fn(RemoteAnalysis $a) => $a->getCountry(),
                );
            }

            public function getCity(): ?string
            {
                return $this->deffer(
                    fn(RemoteAnalysis $a) => $a->getCity(),
                );
            }

            public function getHost(): ?string
            {
                return $this->deffer(
                    fn(RemoteAnalysis $a) => $a->getHost()
                );
            }

            public function getMinQualityScore(): ?int
            {
                return $this->deffer(
                    fn(RemoteAnalysis $a) => $a->getMinQualityScore(),
                );
            }

            public function getMinSequenceLength(): ?int
            {
                return $this->deffer(
                    fn(RemoteAnalysis $a) => $a->getMinSequenceLength(),
                );
            }

            /**
             * @template A
             * @param callable(RemoteAnalysis):A $accessor
             * @param A $alternative
             * @return A
             */
            private function defferOrCached(
                callable $accessor,
                mixed $alternative,
            ) {
                if ($this->real !== null) {
                    return $this->real instanceof RemoteAnalysis ?
                        $accessor($this->real) :
                        $alternative;
                }

                return $alternative;
            }

            /**
             * @template A
             * @param callable(RemoteAnalysis):A $accessor
             * @return A|null
             */
            private function deffer(
                callable $accessor
            ): mixed {
                if ($this->real === null) {
                    $this->real = ($this->fetch)();
                }

                if ($this->real instanceof RemoteAnalysis) {
                    return $accessor($this->real);
                }

                return null;
            }
        };
    }
}
