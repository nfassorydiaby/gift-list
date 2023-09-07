<?php

namespace App\Entity;

use App\Repository\GiftListRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GiftListRepository::class)]
class GiftList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $isPrivate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $passsword = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateOuverture = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFinOuverture = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\ManyToOne(inversedBy: 'giftLists')]
    private ?GiftListTheme $giftListTheme = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

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

    public function isIsPrivate(): ?bool
    {
        return $this->isPrivate;
    }

    public function setIsPrivate(bool $isPrivate): static
    {
        $this->isPrivate = $isPrivate;

        return $this;
    }

    public function getPasssword(): ?string
    {
        return $this->passsword;
    }

    public function setPasssword(?string $passsword): static
    {
        $this->passsword = $passsword;

        return $this;
    }

    public function getDateOuverture(): ?\DateTimeInterface
    {
        return $this->dateOuverture;
    }

    public function setDateOuverture(\DateTimeInterface $dateOuverture): static
    {
        $this->dateOuverture = $dateOuverture;

        return $this;
    }

    public function getDateFinOuverture(): ?\DateTimeInterface
    {
        return $this->dateFinOuverture;
    }

    public function setDateFinOuverture(\DateTimeInterface $dateFinOuverture): static
    {
        $this->dateFinOuverture = $dateFinOuverture;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getGiftListTheme(): ?GiftListTheme
    {
        return $this->giftListTheme;
    }

    public function setGiftListTheme(?GiftListTheme $giftListTheme): static
    {
        $this->giftListTheme = $giftListTheme;

        return $this;
    }
}
