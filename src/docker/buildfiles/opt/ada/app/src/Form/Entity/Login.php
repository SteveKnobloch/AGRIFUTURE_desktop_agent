<?php
declare(strict_types = 1);

namespace App\Form\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Login
{
    public function __construct(
        #[Assert\NotBlank]
        protected ?string $name = null,

        #[Assert\NotBlank]
        protected ?string $username = null,

        #[Assert\NotBlank]
        protected ?string $password = null,
    ) {
    }

    /**
     * @return string|null
     * @api
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @internal
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     * @api
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     * @internal
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string|null
     * @api
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     * @internal
     */
    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

}
