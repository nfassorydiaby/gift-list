<?php 

// src/Entity/BookingGift.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: BookingGiftRepository::class)]
class BookingGift
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private $id;

    #[ORM\Column(length: 255, nullable: true)]
    private $firstName;

    #[ORM\Column(length: 255, nullable: true)]
    private $lastName;


    #[ORM\Column(length: 180)]
    private $email;

    #[ORM\ManyToOne(targetEntity: Gift::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $gift;

    // src/Entity/BookingGift.php

// ... (autres déclarations de propriétés)

// Getters and setters

public function getId(): ?int
{
    return $this->id;
}

public function getFirstName(): ?string
{
    return $this->firstName;
}

public function setFirstName(string $firstName): self
{
    $this->firstName = $firstName;

    return $this;
}

public function getLastName(): ?string
{
    return $this->lastName;
}

public function setLastName(string $lastName): self
{
    $this->lastName = $lastName;

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


