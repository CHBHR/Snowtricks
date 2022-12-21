<?php

namespace App\DataFixtures;

use App\Entity\Figure;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager as PersistenceObjectManager;

class FigureFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(PersistenceObjectManager $manager)
    {
        $date = new \DateTimeImmutable();

        $figure1 = new Figure();
        $figure1->setDescription('Saisie de la carre backside de la planche, entre les deux pieds, avec la main avant');
        $figure1->setDateCreation($date);
        $figure1->setDateModification($date->add(new \DateInterval('P1M')));
        $figure1->setGroupe($this->getReference('grp1'));
        $figure1->setNom('sad');
        $manager->persist($figure1);

        $figure2 = new Figure();
        $figure2->setDescription(
            'Saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière'
        );
        $figure2->setDateCreation($date->add(new \DateInterval('P1D')));
        $figure2->setDateModification($date->add(new \DateInterval('P1D')));
        $figure2->setGroupe($this->getReference('grp1'));
        $figure2->setNom('indy');
        $manager->persist($figure2);

        $figure3 = new Figure();
        $figure3->setDescription(
            'Saisie du carre avant et carre arrière avec chaque main (comme tenir un volant de voiture)'
        );
        $figure3->setDateCreation($date->add(new \DateInterval('PT3H')));
        $figure3->setDateModification($date->add(new \DateInterval('P1MT4H')));
        $figure3->setGroupe($this->getReference('grp3'));
        $figure3->setNom('truck driver');
        $manager->persist($figure3);

        $figure4 = new Figure();
        $figure4->setDescription(
            'Un tour complet en effectuant une rotation horizontale pendant le saut, puis en attérissant en position switch ou normal'
        );
        $figure4->setDateCreation($date->add(new \DateInterval('P2DT3H')));
        $figure4->setDateModification($date->add(new \DateInterval('P2DT3H')));
        $figure4->setGroupe($this->getReference('grp2'));
        $figure4->setNom('360');
        $manager->persist($figure4);

        $figure5 = new Figure();
        $figure5->setDescription(
            'Deux tours complets en effectuant une rotation horizontale pendant le saut, puis en attérissant en position switch ou normal'
        );
        $figure5->setDateCreation($date->add(new \DateInterval('P4DT3H3M')));
        $figure5->setDateModification($date->add(new \DateInterval('P7D')));
        $figure5->setGroupe($this->getReference('grp3'));
        $figure5->setNom('720');
        $manager->persist($figure5);

        $figure6 = new Figure();
        $figure6->setDescription(
            'Trois tours complets en effectuant une rotation horizontale pendant le saut, puis en attérissant en position switch ou normal'
        );
        $figure6->setDateCreation($date->add(new \DateInterval('P1DT6H2M')));
        $figure6->setDateModification($date->add(new \DateInterval('P1DT6H2M')));
        $figure6->setGroupe($this->getReference('grp3'));
        $figure6->setNom('1080');
        $manager->persist($figure6);

        $figure7 = new Figure();
        $figure7->setDescription('Rotation verticale avant');
        $figure7->setDateCreation($date->add(new \DateInterval('P6DT2H9M')));
        $figure7->setDateModification($date->add(new \DateInterval('P12DT12H')));
        $figure7->setGroupe($this->getReference('grp2'));
        $figure7->setNom('Front flip');
        $manager->persist($figure7);

        $figure8 = new Figure();
        $figure8->setDescription('Rotation verticale arrière');
        $figure8->setDateCreation($date->add(new \DateInterval('P3DT3H')));
        $figure8->setDateModification($date->add(new \DateInterval('P3DT3H')));
        $figure8->setGroupe($this->getReference('grp2'));
        $figure8->setNom('Japan air');
        $manager->persist($figure8);

        $figure9 = new Figure();
        $figure9->setDescription('Glisser sur une barre de slide avec l\'avant de la planche sur la barre');
        $figure9->setDateCreation($date->add(new \DateInterval('P10DT2H')));
        $figure9->setDateModification($date->add(new \DateInterval('P10DT2H')));
        $figure9->setGroupe($this->getReference('grp2'));
        $figure9->setNom('Nose slide');
        $manager->persist($figure9);

        $figure10 = new Figure();
        $figure10->setDescription('Glisser sur une barre de slide avec l\'arrière de la planche sur la barre');
        $figure10->setDateCreation($date->add(new \DateInterval('PT12H')));
        $figure10->setDateModification($date->add(new \DateInterval('P1DT12H')));
        $figure10->setGroupe($this->getReference('grp1'));
        $figure10->setNom('Tail slide');
        $manager->persist($figure10);

        $figure11 = new Figure();
        $figure11->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas suscipit rutrum semper. Aenean pharetra quis orci in cursus. Suspendisse aliquam facilisis accumsan. Nullam condimentum nisi sit amet nulla interdum, sit amet accumsan tortor luctus. Sed nec dui in ex ultrices commodo. Praesent varius sagittis dui, ac hendrerit ante congue at. Sed commodo tellus vitae nisi pretium mollis. Suspendisse varius tortor non efficitur consectetur. Nulla consequat, mauris vel pulvinar posuere, odio risus faucibus dolor, sed tempus mauris quam et risus. Aenean ac urna finibus, pharetra augue at, tincidunt libero. Fusce ut tortor elementum, eleifend orci nec, euismod tellus.');
        $figure11->setDateCreation($date);
        $figure11->setDateModification($date);
        $figure11->setGroupe($this->getReference('grp1'));
        $figure11->setNom('Lorem ipsum dolor sit amet');
        $manager->persist($figure11);

        for($i = 0; $i<=10; $i++)
        {
            $figure = new Figure();
            $figure->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas suscipit rutrum semper. Aenean pharetra quis orci in cursus. Suspendisse aliquam facilisis accumsan. Nullam condimentum nisi sit amet nulla interdum, sit amet accumsan tortor luctus. Sed nec dui in ex ultrices commodo. Praesent varius sagittis dui, ac hendrerit ante congue at. Sed commodo tellus vitae nisi pretium mollis. Suspendisse varius tortor non efficitur consectetur. Nulla consequat, mauris vel pulvinar posuere, odio risus faucibus dolor, sed tempus mauris quam et risus. Aenean ac urna finibus, pharetra augue at, tincidunt libero. Fusce ut tortor elementum, eleifend orci nec, euismod tellus.');
            $figure->setDateCreation($date);
            $figure->setDateModification($date);
            $figure->setGroupe($this->getReference('grp1'));
            $figure->setNom($i.' Lorem ipsum dolor sit amet');
            $manager->persist($figure);
        }

        $manager->flush();

        $this->addReference('figure1', $figure1);
        $this->addReference('figure2', $figure2);
        $this->addReference('figure3', $figure3);
        $this->addReference('figure4', $figure4);
        $this->addReference('figure5', $figure5);
        $this->addReference('figure6', $figure6);
        $this->addReference('figure7', $figure7);
        $this->addReference('figure8', $figure8);
        $this->addReference('figure9', $figure9);
        $this->addReference('figure10', $figure10);
    }

    public function getDependencies()
    {
        return array(
            GroupeFixtures::class,
        );
    }
}