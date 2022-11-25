<?php

namespace App\Entity;

use App\Repository\TokenRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: TokenRepository::class)]
class Token implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected int $id;

    /**
     * @param int|null $id
     * @param string|null $token
     * @param string|null $name
     * @param string|null $email
     */
    public function __construct(
        #[ORM\Column(length: 255)]
        public readonly ?string $token,

        #[ORM\Column(length: 255)]
        public readonly ?string $name,

        #[ORM\Column(length: 255)]
        public readonly ?string $email,
    ) {
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials()
    {
        // Noop
    }

    public function getUserIdentifier(): string
    {
        return $this->token;
    }
}
