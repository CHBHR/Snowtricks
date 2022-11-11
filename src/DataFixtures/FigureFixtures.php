<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Figure;

class FigureFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for($i = 1; $i<10; $i++){
            $figure = new Figure();
            $figure ->setNom("Nom de la Figure n°$i")
                    ->setDescription("<p>Description de la figure n°$i</p>")
                    ->setDateCreation(new \DateTime())
                    ->setDateModification(new \DateTime());
            $manager->persist($figure);
        }

        $manager->flush();
    }
}
