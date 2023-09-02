<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class IcsAccessToken implements UserInterface {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string', length: 128, unique: true)]
    private ?string $token = null;

    #[ORM\Column(type: 'string', enumType: IcsAccessTokenType::class)]
    private ?IcsAccessTokenType $type = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?User $user = null;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'string')]
    private ?string $name = null;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    private DateTime $registered;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $lastActive = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string {
        return $this->token;
    }

    public function setToken(string $token): IcsAccessToken {
        $this->token = $token;
        return $this;
    }

    /**
     * @return IcsAccessTokenType|null
     */
    public function getType(): ?IcsAccessTokenType {
        return $this->type;
    }

    public function setType(IcsAccessTokenType $type): IcsAccessToken {
        $this->type = $type;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(User $user): IcsAccessToken {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): IcsAccessToken {
        $this->name = $name;
        return $this;
    }

    public function getRegistered(): DateTime {
        return $this->registered;
    }

    public function getLastActive(): ?DateTime {
        return $this->lastActive;
    }

    public function setLastActive(DateTime $lastActive): IcsAccessToken {
        $this->lastActive = $lastActive;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRoles(): array {
        return $this->user->getRoles();
    }

    /**
     * @inheritDoc
     */
    public function getPassword(): ?string {
        return $this->user->getPassword();
    }

    /**
     * @inheritDoc
     */
    public function getSalt(): ?string {
        return $this->user->getSalt();
    }

    public function eraseCredentials(): void {
        $this->user->eraseCredentials();
    }

    /**
     * @inheritDoc
     */
    public function getUsername(): string {
        return $this->user->getUserIdentifier();
    }

    public function getUserIdentifier(): string {
        return $this->user->getUserIdentifier();
    }
}