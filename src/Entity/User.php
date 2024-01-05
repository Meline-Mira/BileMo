<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity('username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: "Le nom de l'utilisateur est obligatoire")]
    #[Assert\Length(min: 1, max: 180, minMessage: "Le nom de l'utilisateur doit faire au moins {{ limit }} caractères", maxMessage: "Le nom de l'utilisateur ne peut pas faire plus de {{ limit }} caractères")]
    private ?string $username = null;

    /** @var array<string> */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(length: 60)]
    #[Assert\NotBlank(message: 'Le mot de passe est obligatoire')]
    #[Assert\Length(min: 1, max: 60, minMessage: 'Le mot de passe doit faire au moins {{ limit }} caractères', maxMessage: 'Le mot de passe ne peut pas faire plus de {{ limit }} caractères')]
    private ?string $password = null;

    /** @var Collection<int, ClientUser> */
    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: ClientUser::class, orphanRemoval: true)]
    private Collection $clientUsers;

    public function __construct()
    {
        $this->clientUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /** @param array<string> $roles */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, ClientUser>
     */
    public function getClientUsers(): Collection
    {
        return $this->clientUsers;
    }

    public function addClientUser(ClientUser $clientUser): static
    {
        if (!$this->clientUsers->contains($clientUser)) {
            $this->clientUsers->add($clientUser);
            $clientUser->setUser($this);
        }

        return $this;
    }

    public function removeClientUser(ClientUser $clientUser): static
    {
        if ($this->clientUsers->removeElement($clientUser)) {
            // set the owning side to null (unless already changed)
            if ($clientUser->getUser() === $this) {
                $clientUser->setUser(null);
            }
        }

        return $this;
    }
}
