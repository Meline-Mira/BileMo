<?php

namespace App\Entity;

use App\Repository\PictureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PictureRepository::class)]
class Picture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getPhone'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['getPhone'])]
    #[Assert\NotBlank(message: "L'url de l'image est obligatoire")]
    #[Assert\Url(message: "L'url {{ value }} n'est pas une url valide")]
    private ?string $url = null;

    #[ORM\Column(length: 150)]
    #[Groups(['getPhone'])]
    #[Assert\NotBlank(message: "La description de l'image est obligatoire")]
    #[Assert\Length(min: 1, max: 150, minMessage: 'La description doit faire au moins {{ limit }} caractères', maxMessage: 'La descritpion ne peut pas faire plus de {{ limit }} caractères')]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'pictures')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotBlank(message: "L'id du téléphone est obligatoire")]
    private ?Phone $phone = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPhone(): ?Phone
    {
        return $this->phone;
    }

    public function setPhone(?Phone $phone): static
    {
        $this->phone = $phone;

        return $this;
    }
}
