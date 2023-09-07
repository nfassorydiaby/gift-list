<?php

namespace App\Entity;

use App\Repository\GiftListThemeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GiftListThemeRepository::class)]
class GiftListTheme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'giftListTheme', targetEntity: GiftList::class)]
    private Collection $giftLists;

    public function __construct()
    {
        $this->giftLists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, GiftList>
     */
    public function getGiftLists(): Collection
    {
        return $this->giftLists;
    }

    public function addGiftList(GiftList $giftList): static
    {
        if (!$this->giftLists->contains($giftList)) {
            $this->giftLists->add($giftList);
            $giftList->setGiftListTheme($this);
        }

        return $this;
    }

    public function removeGiftList(GiftList $giftList): static
    {
        if ($this->giftLists->removeElement($giftList)) {
            // set the owning side to null (unless already changed)
            if ($giftList->getGiftListTheme() === $this) {
                $giftList->setGiftListTheme(null);
            }
        }

        return $this;
    }
}
