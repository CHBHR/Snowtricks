<?php

namespace App\DataFixtures;

use App\Entity\Groupe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager as PersistenceObjectManager;

class GroupeFixtures extends Fixture
{
    public function load(PersistenceObjectManager $manager)
    {
        $grp1 = new Groupe();
        $grp1->setTitre('Débutant');
        $manager->persist($grp1);

        $grp2 = new Groupe();
        $grp2->setTitre('Intermédiaire');
        $manager->persist($grp2);

        $grp3 = new Groupe();
        $grp3->setTitre('Difficile');
        $manager->persist($grp3);

        $manager->flush();

        $this->addReference('grp1', $grp1);
        $this->addReference('grp2', $grp2);
        $this->addReference('grp3', $grp3);
    }
}
