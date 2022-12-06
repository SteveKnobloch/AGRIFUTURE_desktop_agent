<?php
declare(strict_types = 1);

namespace App\Form\Entity;

use Symfony\Component\Validator\Constraints as Assert;

final class Coordinates
{
    public function __construct(
        #[Assert\Range(min: -180, max: 180)]
        protected ?float $longitude = null,
        #[Assert\Range(min: -90, max: 90)]
        protected ?float $latitude = null,
    ) {}

    /**
     * @api
     */
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    /**
     * @internal
     */
    public function setLongitude(?float $longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @api
     */
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    /**
     * @internal
     */
    public function setLatitude(?float $latitude): void
    {
        $this->latitude = $latitude;
    }


}
