<?php

// src/Entity/AccessToken.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class AccessToken
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private $id;

    #[ORM\Column(length: 64)]
    private $token;

    #[ORM\Column(type: "boolean")]
    private $used = false;


    #[ORM\Column(type: "datetime")]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: GiftList::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $giftList;

    // Getter et Setter pour ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter et Setter pour Token
    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }

    // Getter et Setter pour Used
    public function getUsed(): ?bool
    {
        return $this->used;
    }

    public function setUsed(bool $used): self
    {
        $this->used = $used;
        return $this;
    }

    // Getter et Setter pour CreatedAt
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

     // Getter et Setter pour GiftList
     public function getGiftList(): ?GiftList
     {
         return $this->giftList;
     }
 
     public function setGiftList(?GiftList $giftList): self
     {
         $this->giftList = $giftList;
         return $this;
     }

}
