<?php

namespace App\Entity;

use App\Repository\UrlRepository;
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
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

    #[Assert\Regex('/[.]/', message: 'user.url.no_dot')]
    #[Assert\Length(min: 11, max: 2048)]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $longUrl = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $shortKey = null;

    #[ORM\ManyToOne(inversedBy: 'urls')]
    private ?User $User = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $expirationDate = null;

    const THREE_MONTHS = 'P3M';
    const ONE_MONTH = 'P1M';
    const ONE_WEEK = 'P1M';
    const ONE_MINUTE = 'PT0M5S';

    public function __construct()
    {
        $this->setCreatedAt(new DateTimeImmutable());
        $this->updateExpirationDate(self::ONE_MINUTE);
    }

    public function updateExpirationDate($duration): self
    {
        $today = new DateTimeImmutable();

        $this->setExpirationDate(
            $today->add(new DateInterval($duration))
        );

        return $this;
    }

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

    public function validateUser(UserInterface $user): bool
    {
        return $this->getUser() === $user;
    }

    public function getShortUrl()
    {
        return '/' . $this->getShortKey();
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

    public function hasShortKey(): bool
    {
        return (bool)$this->getShortKey();
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

    public function getExpirationDate(): ?DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(DateTimeInterface $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

}
