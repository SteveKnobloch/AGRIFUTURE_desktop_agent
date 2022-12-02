<?php
namespace App\Entity;

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
        private ?int $id = null,
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }
}
