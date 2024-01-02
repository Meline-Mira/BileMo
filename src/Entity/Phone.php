<?php

namespace App\Entity;

use App\Repository\PhoneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PhoneRepository::class)]
#[UniqueEntity('name')]
class Phone
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getPhones", "getPhone"])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(["getPhones", "getPhone"])]
    #[Assert\NotBlank(message: "Le nom du téléphone est obligatoire")]
    #[Assert\Length(min: 1, max: 100, minMessage: "Le nom doit faire au moins {{ limit }} caractères", maxMessage: "Le nom ne peut pas faire plus de {{ limit }} caractères")]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(["getPhone"])]
    #[Assert\NotBlank(message: "La description du téléphone est obligatoire")]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2)]
    #[Groups(["getPhones", "getPhone"])]
    #[Assert\NotBlank(message: "Le prix du téléphone est obligatoire")]
    private ?float $price = null;

    #[ORM\OneToMany(mappedBy: 'phone', targetEntity: Picture::class, orphanRemoval: true)]
    #[Groups(["getPhone"])]
    private Collection $pictures;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, Picture>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): static
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
            $picture->setPhone($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): static
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getPhone() === $this) {
                $picture->setPhone(null);
            }
        }

        return $this;
    }

    #[Groups(["getPhones"])]
    public function getLinksPhones(): array
    {
        return [
            'self' => ['href' => '/api/phones/'.$this->getId()],
        ];
    }
}
