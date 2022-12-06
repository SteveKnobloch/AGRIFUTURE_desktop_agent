<?php
declare(strict_types = 1);

namespace App\Form\Entity;

use App\Enum\AnalysisStatus;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateStatus
{
    public function __construct(
        #[Assert\NotBlank]
       protected ?AnalysisStatus $status = null,
    ) {}

    /**
     * @api
     */
    public function getStatus(): ?AnalysisStatus
    {
        return $this->status;
    }

    /**
     * @internal
     */
    public function setStatus(AnalysisStatus|string|null $status): void
    {
        if (is_string($status)) {
            $status = AnalysisStatus::tryFrom($status);
        }

        $this->status = $status;
    }
}
