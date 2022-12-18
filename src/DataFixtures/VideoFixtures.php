<?php

namespace App\DataFixtures;

use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager as PersistenceObjectManager;

class VideoFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(PersistenceObjectManager $manager)
    {
        $video1 = new Video();
        $video1->setFigure($this->getReference('figure9'));
        $video1->setUrl('<iframe width="560" height="315" src="https://www.youtube.com/embed/KP6_2qtXlb8" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>');
        $manager->persist($video1);

        $video2 = new Video();
        $video2->setFigure($this->getReference('figure10'));
        $video2->setUrl('<iframe width="560" height="315" src="https://www.youtube.com/embed/KP6_2qtXlb8" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>');
        $manager->persist($video2);

        $video3 = new Video();
        $video3->setFigure($this->getReference('figure10'));
        $video3->setUrl('<iframe width="560" height="315" src="https://www.youtube.com/embed/HRNXjMBakwM" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>');
        $manager->persist($video3);

        $video4 = new Video();
        $video4->setFigure($this->getReference('figure8'));
        $video4->setUrl('<iframe width="560" height="315" src="https://www.youtube.com/embed/CzDjM7h_Fwo" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>');
        $manager->persist($video4);

        $video5 = new Video();
        $video5->setFigure($this->getReference('figure9'));
        $video5->setUrl('<iframe width="560" height="315" src="https://www.youtube.com/embed/oAK9mK7wWvw" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>');
        $manager->persist($video5);

        $video6 = new Video();
        $video6->setFigure($this->getReference('figure6'));
        $video6->setUrl('<iframe width="560" height="315" src="https://www.youtube.com/embed/j4NfAsszIOk" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>');
        $manager->persist($video6);

        $video7 = new Video();
        $video7->setFigure($this->getReference('figure5'));
        $video7->setUrl('<iframe width="560" height="315" src="https://www.youtube.com/embed/4JfBfQpG77o" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>');
        $manager->persist($video7);

        $video8 = new Video();
        $video8->setFigure($this->getReference('figure4'));
        $video8->setUrl('<iframe width="560" height="315" src="https://www.youtube.com/embed/_rS2i4-yb6E" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>');
        $manager->persist($video8);

        $video9 = new Video();
        $video9->setFigure($this->getReference('figure2'));
        $video9->setUrl('<iframe width="560" height="315" src="https://www.youtube.com/embed/iKkhKekZNQ8" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>');
        $manager->persist($video9);

        $video10 = new Video();
        $video10->setFigure($this->getReference('figure3'));
        $video10->setUrl('<iframe width="560" height="315" src="https://www.youtube.com/embed/ApPcNktK62M" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>');
        $manager->persist($video10);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            FigureFixtures::class,
        );
    }
}