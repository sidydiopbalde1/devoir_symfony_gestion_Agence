<?php

namespace App\DataFixtures;

use App\Entity\Agence;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AgenceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
        $agence = new Agence();
        $agence->setNumero('Agence'.$i);
        $agence->setAdresse('Dakar');
        $agence->setTelephone('0123456789');
        $manager->persist($agence);
        }

        $manager->flush();
    }
}
