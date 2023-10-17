<?php 

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingGiftRepository::class)]
class BookingGift
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $fullName;

    #[ORM\Column(type: "string", length: 255)]
    private $email;

    #[ORM\ManyToOne(targetEntity: Gift::class, inversedBy: "bookings")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Gift $gift;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;
        return $this;
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

    public function getGift(): ?Gift
    {
        return $this->gift;
    }

    public function setGift(?Gift $gift): self
    {
        $this->gift = $gift;
        return $this;
    }

}
