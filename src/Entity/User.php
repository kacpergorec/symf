<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Util\BubbleRenderer;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use RuntimeException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @method string getUserIdentifier()
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastname = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $username = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column]
    private array $roles = ['ROLE_USER'];

    #[ORM\Column]
    private ?bool $verified = false;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'User', targetEntity: Url::class, cascade: ['persist', 'remove'])]
    private Collection $urls;

    public function __construct()
    {
        $this->setCreatedAt(new DateTimeImmutable());
        $this->urls = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPersonalData()
    {
        if ($this->getFirstname() && $this->getLastname()) {
            return $this->getFirstname() . ' ' . $this->getLastname();
        }

        return $this->getUsername();
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = ucfirst($firstname);

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = ucfirst($lastname);

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getUserInfoArray(): array
    {
        $bubbleRenderer = new BubbleRenderer();

        return [
            'user.firstname' => $this->getFirstname(),
            'user.lastname' => $this->getLastname(),
            'user.username' => $this->getUsername(),
            'user.email' => $this->getEmail(),
            'user.roles' => $bubbleRenderer->renderBubbles($this->getRoles()),
        ];
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRoles()
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function addRole(string $role): self
    {
        $this->roles[] = $role;

        return $this;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function hasPassword(): bool
    {
        return (bool)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->verified;
    }

    public function setVerified(bool $verified): self
    {
        $this->verified = $verified;

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

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt()
    {
        return null;
    }

    public function __call(string $name, array $arguments)
    {
        // TODO: Implement @method string getUserIdentifier()
    }

    public function __toString()
    {
        return $this->getFirstname() ?: $this->getUsername();
    }

    /**
     * @return Collection<int, Url>
     */
    public function getUrls(): Collection
    {
        return $this->urls;
    }

    public function getUrlKeys(): array
    {
        return array_map(
            static fn($url) => $url->getShortKey(),
            $this->getUrls()->toArray()
        );
    }

    public function addUrl(Url $url): self
    {
        if (!$this->urls->contains($url)) {
            $this->urls->add($url);
            $url->setUser($this);
        }

        return $this;
    }

    public function removeUrl(Url $url): self
    {
        if ($this->urls->removeElement($url)) {
            // set the owning side to null (unless already changed)
            if ($url->getUser() === $this) {
                $url->setUser(null);
            }
        }

        return $this;
    }

    /**
     *  Updates given fields of User.
     *
     *  TODO: Make sure it is okay to throw Exceptions via Entities.
     */
    public function updateFields(array $fields): self
    {
        foreach ($fields as $propertyName => $field) {
            $method = "set" . ucfirst(strtolower($propertyName));

            if (!method_exists($this, $method)) {
                throw new RuntimeException("Method {$method} does not exist.");
            }

            $this->$method($field);
        }

        return $this;
    }

    /**
     *  Updates User fields but only if all of them are empty.
     */
    public function updateFieldsIfEmpty(array $fields): self
    {
        $usedProperties = array_filter(get_object_vars($this));

        //If given fields are not empty - dont update the user.
        if (array_intersect(
            array_keys($fields),
            array_keys($usedProperties)
        )) {
            return $this;
        }

        return $this->updateFields($fields);
    }
}
