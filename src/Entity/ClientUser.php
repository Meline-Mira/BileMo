<?php

namespace App\Entity;

use App\Repository\ClientUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: ClientUserRepository::class)]
class ClientUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getClients", "getClient"])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(["getClient"])]
    #[Assert\NotBlank(message: "Le prénom du client est obligatoire")]
    #[Assert\Length(min: 1, max: 100, minMessage: "Le prénom doit faire au moins {{ limit }} caractères", maxMessage: "Le prénom ne peut pas faire plus de {{ limit }} caractères")]
    private ?string $firstName = null;

    #[ORM\Column(length: 100)]
    #[Groups(["getClient"])]
    #[Assert\NotBlank(message: "Le nom du client est obligatoire")]
    #[Assert\Length(min: 1, max: 100, minMessage: "Le nom doit faire au moins {{ limit }} caractères", maxMessage: "Le nom ne peut pas faire plus de {{ limit }} caractères")]
    private ?string $lastName = null;

    #[ORM\Column(length: 100)]
    #[Groups(["getClients", "getClient"])]
    #[Assert\NotBlank(message: "L'email du client est obligatoire")]
    #[Assert\Length(min: 1, max: 100, minMessage: "L'email' doit faire au moins {{ limit }} caractères", maxMessage: "L'email ne peut pas faire plus de {{ limit }} caractères")]
    #[Assert\Email(message: "L'email {{ value }} n'est pas un email valide.")]
    private ?string $email = null;

    #[ORM\ManyToOne(inversedBy: 'clientUsers')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotBlank(message: "L'id de l'utilisateur lié au client est obligatoire")]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    #[OA\Property(
        properties: [
            new OA\Property(property: 'self', properties: [new OA\Property(property: 'href', type: 'string')], type: 'object'),
            new OA\Property(property: 'delete', properties: [new OA\Property(property: 'href', type: 'string')], type: 'object'),
        ],
        type: 'object'
    )]
    #[Groups(["getClients"])]
    #[SerializedName('links')]
    public function getLinksClients(): array
    {
        return [
            'self' => ['href' => '/api/clients/'.$this->getId()],
            'delete' => ['href' => '/api/clients/'.$this->getId()],
        ];
    }

    #[OA\Property(
        properties: [
            new OA\Property(property: 'delete', properties: [new OA\Property(property: 'href', type: 'string')], type: 'object'),
            new OA\Property(property: 'self', properties: [new OA\Property(property: 'href', type: 'string')], type: 'object'),
        ],
        type: 'object',
    )]
    #[Groups(["getClient"])]
    #[SerializedName('links')]
    public function getLinksClient(): array
    {
        return [
            'delete' => ['href' => '/api/clients/'.$this->getId()],
            'self' => ['href' => '/api/clients'],
        ];
    }
}
