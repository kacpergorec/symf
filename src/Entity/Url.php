<?php

namespace App\Entity;

use App\Repository\UrlRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: UrlRepository::class)]
class Url
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

//    #[Assert\Regex('/[.]/',message: 'user.url.no_dot')]
    #[Assert\Length(min: 11, max: 2048)]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $longUrl = null;

    #[ORM\Column(length: 255)]
    private ?string $shortKey = null;

    #[ORM\ManyToOne(inversedBy: 'urls')]
    private ?User $User = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLongUrl(): ?string
    {
        return $this->longUrl;
    }

    public function setLongUrl(string $longUrl): self
    {
        $this->longUrl = $longUrl;

        return $this;
    }

    public function getShortKey(): ?string
    {
        return $this->shortKey;
    }

    public function setShortKey(string $shortKey): self
    {
        $this->shortKey = $shortKey;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?UserInterface $User): self
    {
        $this->User = $User;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
