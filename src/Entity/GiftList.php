<?php

namespace App\Entity;

use App\Repository\GiftListRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: GiftListRepository::class)]
#[Vich\Uploadable]
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
    /**
     * @Assert\NotBlank(groups={"PasswordRequired"})
     */
    private ?string $password = null;

    #[ORM\Column]
    private $isArchived = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateOuverture = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFinOuverture = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\ManyToMany(targetEntity: GiftListTheme::class,inversedBy: 'giftLists')]
    private $giftListThemes = null;

    #[ORM\ManyToOne(targetEntity: User::class,inversedBy: 'giftLists')]
    private $user;

    #[Vich\UploadableField(mapping: "cover_image", fileNameProperty: "coverName")]
    private $coverFile;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $coverName;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private $updatedAt;

    #[ORM\OneToMany(targetEntity: Gift::class, mappedBy: "giftList", cascade: ["persist", "remove"])]
    private Collection $gifts;



    public function __construct()
    {
        $this->giftListThemes = new ArrayCollection();
        $this->gifts = new ArrayCollection();

    }

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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

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

    public function getGiftListThemes(): Collection
    {
        return $this->giftListThemes;
    }
    
    public function addGiftListTheme(GiftListTheme $giftListTheme): self
    {
        if (!$this->giftListThemes->contains($giftListTheme)) {
            $this->giftListThemes->add($giftListTheme);
            $giftListTheme->addGiftList($this); // Assurez-vous d'avoir cette méthode dans GiftListTheme
        }
        return $this;
    }
    
    public function removeGiftListTheme(GiftListTheme $giftListTheme): self
    {
        if ($this->giftListThemes->contains($giftListTheme)) {
            $this->giftListThemes->removeElement($giftListTheme);
            $giftListTheme->removeGiftList($this); // Assurez-vous d'avoir cette méthode dans GiftListTheme
        }
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function isArchived(): ?bool
    {
        return $this->isArchived;
    }

    public function setArchived(bool $isArchived): self
    {
        $this->isArchived = $isArchived;
        return $this;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ($this->getDateOuverture() && $this->getDateFinOuverture() && $this->getDateOuverture() >= $this->getDateFinOuverture()) {
            $context->buildViolation('La date d\'ouverture doit être antérieure à la date de fermeture.')
                ->atPath('dateOuverture')
                ->addViolation();
        }

        if ($this->isPrivate && empty($this->password)) {
            $context->buildViolation('Password is required when isPrivate is true.')
                ->atPath('password')
                ->addViolation();
        }
    }

    public function setCoverFile(?File $coverFile = null): void
    {
        $this->coverFile = $coverFile;

        if (null !== $coverFile) {
            // "touch" l'entité pour forcer une mise à jour dans la base de données
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getCoverFile(): ?File
    {
        return $this->coverFile;
    }

    public function setCoverName(?string $coverName): void
    {
        $this->coverName = $coverName;
    }

    public function getCoverName(): ?string
    {
        return $this->coverName;
    }
    
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

     /**
     * @return Collection|Gift[]
     */
    public function getGifts(): Collection
    {
        return $this->gifts;
    }

    public function addGift(Gift $gift): self
    {
        if (!$this->gifts->contains($gift)) {
            $this->gifts[] = $gift;
            $gift->setGiftList($this);
        }

        return $this;
    }

    public function removeGift(Gift $gift): self
    {
        // set the owning side to null (unless already changed)
        if ($this->gifts->removeElement($gift) && $gift->getGiftList() === $this) {
            $gift->setGiftList(null);
        }

        return $this;
    }

}
