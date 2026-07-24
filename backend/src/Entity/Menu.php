<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['menu:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    #[Groups(['menu:read', 'menu:write'])]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['menu:read', 'menu:write'])]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['menu:read', 'menu:write'])]
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/\.(jpg|jpeg|png|webp)$/i',
        message: 'Le fichier doit être une image jpg, jpeg, png ou webp.'
    )]
    private ?string $image = null;

    #[ORM\Column]
    #[Groups(['menu:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['menu:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Food>
     */
    #[ORM\ManyToMany(targetEntity: Food::class, inversedBy: 'menus')]
    private Collection $foods;

    public function __construct()
    {
        $this->foods = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

     public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Food>
     */
    public function getFoods(): Collection
    {
        return $this->foods;
    }

    public function addFood(Food $food): static
    {
        if (!$this->foods->contains($food)) {
            $this->foods->add($food);
        }

        return $this;
    }

    public function removeFood(Food $food): static
    {
        $this->foods->removeElement($food);

        return $this;
    }
}