<?php
declare(strict_types = 1);

namespace App\Form\Entity;

use App\Enum\AnalysisType;
use App\Enum\FileFormat;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
final class AnalysisInput
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        private ?string $name = null,
        #[Assert\Length(max: 512)]
        private ?string $host = null,
        #[Assert\NotBlank]
        #[Assert\Country]
        private ?string $country = null,
        #[Assert\Length(max: 128)]
        private ?string $city = null,
        #[Assert\Valid]
        private ?Coordinates $coordinates = null,
        private ?string $directory = null,
        #[Assert\NotBlank]
        private ?AnalysisType $type = null,
        private bool $subSpeciesLevel = false,
        private bool $sensitiveMode = false,
        #[Assert\NotBlank]
        private ?FileFormat $format = null,
        #[Assert\Callback([self::class, 'requiredForFast5'])]
        private ?string $flowcellType = null,
        #[Assert\Callback([self::class, 'requiredForFast5'])]
        private ?string $libraryToolkit = null,
        private int $minQualityScore = 8,
        private int $minSequenceLength = 8,
        #[Assert\NotBlank]
        private ?bool $termsOfServiceAccepted = null,
    ) {
    }

    /**
     * @api
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @api
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @api
     */
    public function getDirectory(): ?string
    {
        return $this->directory;
    }

    /**
     * @api
     */
    public function getFlowcellType(): ?string
    {
        return $this->flowcellType;
    }

    /**
     * @api
     */
    public function getFormat(): ?FileFormat
    {
        return $this->format;
    }

    /**
     * @api
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * @api
     */
    public function getLibraryToolkit(): ?string
    {
        return $this->libraryToolkit;
    }

    /**
     * @api
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @api
     */
    public function getTermsOfServiceAccepted(): ?bool
    {
        return $this->termsOfServiceAccepted;
    }

    /**
     * @api
     */
    public function getType(): ?AnalysisType
    {
        return $this->type;
    }

    public static function requiredForFast5(
        mixed $_,
        ExecutionContextInterface $context,
    ) {
        if ($context->getObject()->format === FileFormat::fast5 &&
            !$context->getValue()
        ) {
            $context->addViolation(
                'This value should not be blank.'
            );
        }
    }

    /**
     * @internal
     */
    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    /**
     * @internal
     */
    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    /**
     * @internal
     */
    public function setDirectory(?string $directory): void
    {
        $this->directory = $directory;
    }

    /**
     * @internal
     */
    public function setFlowcellType(?string $flowcellType): void
    {
        $this->flowcellType = $flowcellType;
    }

    /**
     * @internal
     */
    public function setFormat(?FileFormat $format): void
    {
        $this->format = $format;
    }

    /**
     * @internal
     */
    public function setHost(?string $host): void
    {
        $this->host = $host;
    }

    /**
     * @internal
     */
    public function setLibraryToolkit(?string $libraryToolkit): void
    {
        $this->libraryToolkit = $libraryToolkit;
    }

    /**
     * @internal
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @internal
     */
    public function setTermsOfServiceAccepted(?bool $termsOfServiceAccepted
    ): void {
        $this->termsOfServiceAccepted = $termsOfServiceAccepted;
    }

    /**
     * @internal
     */
    public function setType(?AnalysisType $type): void
    {
        $this->type = $type;
    }

    /**
     * @api
     */
    public function getCoordinates(): ?Coordinates
    {
        return $this->coordinates;
    }

    /**
     * @internal
     */
    public function setCoordinates(?Coordinates $coordinates): void
    {
        $this->coordinates = $coordinates;
    }

    /**
     * @api
     */
    public function getMinQualityScore(): int
    {
        return $this->minQualityScore;
    }

    /**
     * @internal
     */
    public function setMinQualityScore(int $minQualityScore): void
    {
        $this->minQualityScore = $minQualityScore;
    }

    /**
     * @api
     */
    public function getMinSequenceLength(): int
    {
        return $this->minSequenceLength;
    }

    /**
     * @internal
     */
    public function setMinSequenceLength(int $minSequenceLength): void
    {
        $this->minSequenceLength = $minSequenceLength;
    }

    /**
     * @api
     */
    public function isSubSpeciesLevel(): bool
    {
        return $this->subSpeciesLevel;
    }

    /**
     * @internal
     */
    public function setSubSpeciesLevel(bool $subSpeciesLevel): void
    {
        $this->subSpeciesLevel = $subSpeciesLevel;
    }

    /**
     * @api
     */
    public function isSensitiveMode(): bool
    {
        return $this->sensitiveMode;
    }

    /**
     * @internal
     */
    public function setSensitiveMode(bool $sensitiveMode): void
    {
        $this->sensitiveMode = $sensitiveMode;
    }
}
