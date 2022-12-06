<?php
declare(strict_types = 1);

namespace App\Entity;

use App\Enum\AnalysisStatus;
use App\Enum\AnalysisType;
use App\Enum\FileFormat;

class RemoteAnalysis
{
    /**
     * @param iterable<RemoteUpload> $uploaded
     */
    private function __construct(
        protected readonly int $id,
        protected readonly AnalysisType $type,
        protected readonly ?bool $subSpeciesLevel,
        protected readonly ?bool $sensitveMode,
        protected readonly AnalysisStatus $status,
        protected readonly \DateTimeInterface $created,
        protected readonly ?\DateTimeInterface $lastReport,
        protected readonly \DateTimeInterface $runUntil,
        protected readonly ?\DateTimeImmutable $finishedTime,
        protected readonly ?string $finishedReason,
        protected readonly iterable $uploaded,
        protected readonly FileFormat $format,
        protected readonly ?string $flowcellType,
        protected readonly ?string $libraryToolkit,
        protected readonly string $name,
        protected readonly ?float $latitude,
        protected readonly ?float $longitude,
        protected readonly string $country,
        protected readonly ?string $city,
        protected readonly ?string $host,
        protected readonly int $minQualityScore,
        protected readonly int $minSequenceLength,
    ) {}

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return \App\Enum\AnalysisType
     */
    public function getType(): ?AnalysisType
    {
        return $this->type;
    }

    /**
     * @return bool|null
     */
    public function getSubSpeciesLevel(): ?bool
    {
        return $this->subSpeciesLevel;
    }

    /**
     * @return bool|null
     */
    public function getSensitiveMode(): ?bool
    {
        return $this->sensitveMode;
    }

    /**
     * @return \App\Enum\AnalysisStatus
     */
    public function getStatus(): ?AnalysisStatus
    {
        return $this->status;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getLastReport(): ?\DateTimeInterface
    {
        return $this->lastReport;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getRunUntil(): ?\DateTimeInterface
    {
        return $this->runUntil;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getFinishedTime(): ?\DateTimeImmutable
    {
        return $this->finishedTime;
    }

    /**
     * @return string|null
     */
    public function getFinishedReason(): ?string
    {
        return $this->finishedReason;
    }

    /**
     * @return iterable<\App\Entity\RemoteUpload>|null
     */
    public function getUploaded(): ?iterable
    {
        return $this->uploaded;
    }

    public function getFormat(): ?FileFormat
    {
        return $this->format;
    }

    public function getFlowcellType(): ?string
    {
        return $this->flowcellType;
    }

    /**
     * @return string|null
     */
    public function getLibraryToolkit(): ?string
    {
        return $this->libraryToolkit;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function getMinQualityScore(): ?int
    {
        return $this->minQualityScore;
    }

    public function getMinSequenceLength(): ?int
    {
        return $this->minSequenceLength;
    }

    public static function fromResponse(\stdClass $json) {
        return new self(
            $json->id,
            AnalysisType::from($json->type->type),
            $json->type->subSpeciedLevel ?? null,
            $json->type->sensitiveMode ?? null,
            AnalysisStatus::tryFrom($json->status) ??
                AnalysisStatus::unknown,
            new \DateTimeImmutable($json->created),
            isset($json->lastReport) ?
                new \DateTimeImmutable($json->lastReport) :
                null,
            new \DateTimeImmutable($json->runUntil),
            (($json->finished ?? null)?->time ?? null) ?
                new \DateTimeImmutable($json->finished->time) :
                null,
            ($json->finished ?? null)?->reason ?? null,
            array_map(
                fn(\stdClass $upload) => new RemoteUpload(
                    $upload->fileName,
                    $upload->size ?? null,
                    $upload->crc32 ?? null,
                ),
                $json->uploaded,
            ),
            FileFormat::from($json->format->type),
            $json->format->flowcellType ?? null,
            $json->format->libraryToolkit ?? null,
            $json->name,
            $json->location->latitude ?? null,
            $json->location->longitude ?? null,
            $json->location->country,
            $json->location->city ?? null,
            $json->host ?? null,
            $json->minQualityScore,
            $json->minSequenceLength,
        );
    }
}
