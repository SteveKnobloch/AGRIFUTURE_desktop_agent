<?php
namespace App\Entity;

use App\Enum\UploadFileError;
use App\Repository\UploadRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UploadRepository::class)]
class Upload
{

    public function __construct(
        #[ORM\Column(length: 255)]
        public readonly string $fileName,

        #[ORM\ManyToOne(inversedBy: 'uploads')]
        #[ORM\JoinColumn(nullable: false, referencedColumnName: 'local_uuid')]
        public readonly Analysis $analysis,

        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column]
        protected ?int $id = null,

        #[ORM\Column(nullable: true)]
        protected ?UploadFileError $error = null,

        #[ORM\Column]
        private bool $uploaded = false,
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getError(): ?UploadFileError
    {
        return $this->error;
    }

    public function setError(?UploadFileError $error): void
    {
        $this->error = $error;
    }

    /**
     * @return bool
     */
    public function isUploaded(): bool
    {
        return $this->uploaded;
    }

    /**
     * @param bool $uploaded
     */
    public function setUploaded(): void
    {
        $this->uploaded = true;
    }


}
