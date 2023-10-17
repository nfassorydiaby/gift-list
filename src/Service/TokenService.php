<?php 

// src/Service/TokenService.php
namespace App\Service;

use App\Entity\AccessToken;
use App\Entity\GiftList;
use Doctrine\ORM\EntityManagerInterface;

class TokenService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createToken(GiftList $giftList): AccessToken
    {
        $accessToken = new AccessToken();
        $accessToken->setToken(bin2hex(random_bytes(16))); // 32 chars
        $accessToken->setCreatedAt(new \DateTime());
        $accessToken->setGiftList($giftList);

        $this->entityManager->persist($accessToken);
        $this->entityManager->flush();

        return $accessToken;
    }

    public function verifyToken(string $token): ?AccessToken
    {
        $accessToken = $this->entityManager->getRepository(AccessToken::class)->findOneBy([
            'token' => $token,
            'used' => false,
        ]);

        if ($accessToken && $this->isTokenExpired($accessToken)) {
            // Marquer le token comme utilisé et sauvegarder
            $accessToken->setUsed(true);
            $this->entityManager->flush();

            return $accessToken;
        }

        return null; // ou vous pouvez lancer une exception
    }

    private function isTokenExpired(AccessToken $accessToken): bool
    {
        $expiryDate = (clone $accessToken->getCreatedAt())->modify('+1 day'); // 24 heures de validité

        return $expiryDate > new \DateTime();
    }
}
