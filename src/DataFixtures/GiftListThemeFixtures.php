<?php
// src/DataFixtures/GiftListThemeFixtures.php

namespace App\DataFixtures;

use App\Entity\GiftListTheme;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GiftListThemeFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $themes = [
            'Anniversaire',
            'Mariage',
            'Naissance',
            'Baptême',
            'Pot de départ',
            'Crémaillère'
        ];

        foreach ($themes as $themeName) {
            $theme = new GiftListTheme();
            $theme->setNom($themeName);
            $manager->persist($theme);
        }

        $manager->flush();
    }
}
