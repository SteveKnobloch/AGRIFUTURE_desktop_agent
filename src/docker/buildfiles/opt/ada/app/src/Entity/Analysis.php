<?php

namespace App\Entity;

use App\Repository\AnalysisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnalysisRepository::class)]
class Analysis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $remotePipelineId = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 36)]
    private ?string $localUuid = null;

    #[ORM\OneToMany(mappedBy: 'analysis', targetEntity: Upload::class, orphanRemoval: true)]
    private Collection $uploads;

    #[ORM\Column]
    private ?bool $paused = null;

    #[ORM\Column(length: 1024)]
    private ?string $relativeDataPath = null;

    #[ORM\Column(length: 5)]
    private ?string $fileType = null;

    public function __construct()
    {
        $this->uploads = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRemotePipelineId(): ?int
    {
        return $this->remotePipelineId;
    }

    public function setRemotePipelineId(int $remotePipelineId): self
    {
        $this->remotePipelineId = $remotePipelineId;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLocalUuid(): ?string
    {
        return $this->localUuid;
    }

    public function setLocalUuid(string $localUuid): self
    {
        $this->localUuid = $localUuid;

        return $this;
    }

    /**
     * @return Collection<int, Upload>
     */
    public function getUploads(): Collection
    {
        return $this->uploads;
    }

    public function addUpload(Upload $upload): self
    {
        if (!$this->uploads->contains($upload)) {
            $this->uploads->add($upload);
            $upload->setAnalysis($this);
        }

        return $this;
    }

    public function removeUpload(Upload $upload): self
    {
        if ($this->uploads->removeElement($upload)) {
            // set the owning side to null (unless already changed)
            if ($upload->getAnalysis() === $this) {
                $upload->setAnalysis(null);
            }
        }

        return $this;
    }

    public function isPaused(): ?bool
    {
        return $this->paused;
    }

    public function setPaused(bool $paused): self
    {
        $this->paused = $paused;

        return $this;
    }

    public function getRelativeDataPath(): ?string
    {
        return $this->relativeDataPath;
    }

    public function setRelativeDataPath(string $relativeDataPath): self
    {
        $this->relativeDataPath = $relativeDataPath;

        return $this;
    }

    public function getFileType(): ?string
    {
        return $this->fileType;
    }

    public function setFileType(string $fileType): self
    {
        $this->fileType = $fileType;

        return $this;
    }
}
