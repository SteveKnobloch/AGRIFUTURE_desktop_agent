<?php

namespace App\Entity;

use App\Enum\AnalysisStatus;
use App\Enum\FileFormat;
use App\Repository\AnalysisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: AnalysisRepository::class)]
class Analysis
{

    #[ORM\OneToMany(
        mappedBy: 'analysis',
        targetEntity: Upload::class,
        orphanRemoval: true
    )]
    private Collection $uploads;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid')]
        public readonly Uuid $localUuid,

        #[ORM\Column]
        public readonly int $remotePipelineId,

        #[ORM\Column(length: 255)]
        public readonly string $name,

        #[ORM\Column(length: 1024)]
        public readonly string $relativeDataPath,

        #[ORM\Column(length: 5)]
        public readonly FileFormat $fileType,

        #[ORM\Column(options: ['default' => AnalysisStatus::running])]
        protected AnalysisStatus $status = AnalysisStatus::running,
    ) {
        $this->uploads = new ArrayCollection();
    }

    /** @return Collection<Upload> */
    public function getUploads(): Collection
    {
        return $this->uploads;
    }

    public function getStatus(): AnalysisStatus
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus(AnalysisStatus $status): void
    {
        $this->status = $status;
    }
}
