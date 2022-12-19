<?php

namespace App\DataFixtures;

use App\Entity\Images;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager as PersistenceObjectManager;

class ImageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(PersistenceObjectManager $manager)
    {
        $image1 = new Images();
        $image1->setNom('sad01.jpg');
        $image1->setFigure($this->getReference('figure1'));
        $manager->persist($image1);

        $image2 = new Images();
        $image2->setNom('sad02.jpg');
        $image2->setFigure($this->getReference('figure1'));
        $manager->persist($image2);

        $image3 = new Images();
        $image3->setNom('sad03.jpg');
        $image3->setFigure($this->getReference('figure1'));
        $manager->persist($image3);

        $image4 = new Images();
        $image4->setNom('indy01.jpg');
        $image4->setFigure($this->getReference('figure2'));
        $manager->persist($image4);

        $image5 = new Images();
        $image5->setNom('indy02.jpg');
        $image5->setFigure($this->getReference('figure2'));
        $manager->persist($image5);

        $image6 = new Images();
        $image6->setNom('indy03.jpg');
        $image6->setFigure($this->getReference('figure2'));
        $manager->persist($image6);

        $image7 = new Images();
        $image7->setNom('indy04.jpeg');
        $image7->setFigure($this->getReference('figure2'));
        $manager->persist($image7);

        $image8 = new Images();
        $image8->setNom('truckdriver01.jpeg');
        $image8->setFigure($this->getReference('figure3'));
        $manager->persist($image8);

        $image9 = new Images();
        $image9->setNom('truckdriver02.jpg');
        $image9->setFigure($this->getReference('figure3'));
        $manager->persist($image9);

        $image10 = new Images();
        $image10->setNom('truckdriver03.jpg');
        $image10->setFigure($this->getReference('figure3'));
        $manager->persist($image10);

        $image11 = new Images();
        $image11->setNom('360_01.jpg');
        $image11->setFigure($this->getReference('figure4'));
        $manager->persist($image11);

        $image12 = new Images();
        $image12->setNom('360_02.jpg');
        $image12->setFigure($this->getReference('figure4'));
        $manager->persist($image12);
        $manager->flush();

        $image13 = new Images();
        $image13->setNom('720_01.jpg');
        $image13->setFigure($this->getReference('figure5'));
        $manager->persist($image13);
        $manager->flush();

        $image14 = new Images();
        $image14->setNom('720_02.jpg');
        $image14->setFigure($this->getReference('figure5'));
        $manager->persist($image14);
        $manager->flush();

        $image15 = new Images();
        $image15->setNom('1080_01.jpg');
        $image15->setFigure($this->getReference('figure6'));
        $manager->persist($image15);
        $manager->flush();

        $image16 = new Images();
        $image16->setNom('1080_02.jpg');
        $image16->setFigure($this->getReference('figure6'));
        $manager->persist($image16);
        $manager->flush();

        $image17 = new Images();
        $image17->setNom('Frontflip01.jpg');
        $image17->setFigure($this->getReference('figure7'));
        $manager->persist($image17);
        $manager->flush();

        $image18 = new Images();
        $image18->setNom('Frontflip02.jpg');
        $image18->setFigure($this->getReference('figure7'));
        $manager->persist($image18);
        $manager->flush();

        $image19 = new Images();
        $image19->setNom('japanair01.jpg');
        $image19->setFigure($this->getReference('figure8'));
        $manager->persist($image19);
        $manager->flush();

        $image20 = new Images();
        $image20->setNom('japanair02.jpg');
        $image20->setFigure($this->getReference('figure8'));
        $manager->persist($image20);
        $manager->flush();

        $image21 = new Images();
        $image21->setNom('Noseslide01.jpg');
        $image21->setFigure($this->getReference('figure9'));
        $manager->persist($image21);
        $manager->flush();

        $image22 = new Images();
        $image22->setNom('Noseslide02.jpg');
        $image22->setFigure($this->getReference('figure9'));
        $manager->persist($image22);
        $manager->flush();

        $image23 = new Images();
        $image23->setNom('Tailslide01.jpg');
        $image23->setFigure($this->getReference('figure10'));
        $manager->persist($image23);
        $manager->flush();

        $image24 = new Images();
        $image24->setNom('Tailslide02.jpg');
        $image24->setFigure($this->getReference('figure10'));
        $manager->persist($image24);
        $manager->flush();

        $avatar01 = new Images();
        $avatar01->setNom('jodo.jpg');
        $avatar01->setUtilisateurId($this->getReference('user1'));
        $manager->persist($avatar01);
        $manager->flush();

        $avatar02 = new Images();
        $avatar02->setNom('gandalf.jpg');
        $avatar02->setUtilisateurId($this->getReference('user2'));
        $manager->persist($avatar02);
        $manager->flush();

        $avatar03 = new Images();
        $avatar03->setNom('defaultAvatar.jpg');
        $avatar03->setUtilisateurId($this->getReference('user3'));
        $manager->persist($avatar03);
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            FigureFixtures::class,
            UtilisateurFixtures::class
        );
    }
}