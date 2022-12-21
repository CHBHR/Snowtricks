<?php

namespace App\DataFixtures;

use App\Entity\Commentaire;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager as PersistenceObjectManager;

class CommentaireFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(PersistenceObjectManager $manager)
    {
        $date = new \DateTimeImmutable();

        $comment1 = new Commentaire();
        $comment1->setContenu("J'ai réussit à la faire celle là l'autre jour! Trop content");
        $comment1->setDateCreation(new \DateTime());
        $comment1->setFigure($this->getReference('figure5'));
        $comment1->setAuteur($this->getReference('user2'));
        $manager->persist($comment1);

        $comment2 = new Commentaire();
        $comment2->setContenu("Franchement celle là elle fait toujours effet");
        $comment2->setDateCreation(new \DateTime());
        $comment2->setFigure($this->getReference('figure2'));
        $comment2->setAuteur($this->getReference('user1'));
        $manager->persist($comment2);

        $comment3 = new Commentaire();
        $comment3->setContenu('J\'adorerais réussir celui ci mais il me faut plus d\'entrainement');
        $comment3->setDateCreation(new \DateTime());
        $comment3->setFigure($this->getReference('figure3'));
        $comment3->setAuteur($this->getReference('user1'));
        $manager->persist($comment3);

        $comment4 = new Commentaire();
        $comment4->setContenu("Une figure pas si facile que ça");
        $comment4->setDateCreation($date->add(new \DateInterval('P7DT23H')));
        $comment4->setFigure($this->getReference('figure10'));
        $comment4->setAuteur($this->getReference('user3'));
        $manager->persist($comment4);

        $comment5 = new Commentaire();
        $comment5->setContenu('Magnifique !');
        $comment5->setDateCreation($date->add(new \DateInterval('P15DT4H5M')));
        $comment5->setFigure($this->getReference('figure10'));
        $comment5->setAuteur($this->getReference('user1'));
        $manager->persist($comment5);

        $comment6 = new Commentaire();
        $comment6->setContenu("Franchement je ne la trouve pas si compliquée à faire");
        $comment6->setDateCreation($date->add(new \DateInterval('P16DT15H15M')));
        $comment6->setFigure($this->getReference('figure10'));
        $comment6->setAuteur($this->getReference('user2'));
        $manager->persist($comment6);

        $comment7 = new Commentaire();
        $comment7->setContenu("Une belle figure quand même ;)");
        $comment7->setDateCreation($date->add(new \DateInterval('P18DT4H7M')));
        $comment7->setFigure($this->getReference('figure10'));
        $comment7->setAuteur($this->getReference('user2'));
        $manager->persist($comment7);

        $comment8 = new Commentaire();
        $comment8->setContenu("Il y en a qui se la pêtes ici j'ai l'impresison :P");
        $comment8->setDateCreation($date->add(new \DateInterval('P18DT5H6M')));
        $comment8->setFigure($this->getReference('figure10'));
        $comment8->setAuteur($this->getReference('user3'));
        $manager->persist($comment8);

        $comment9 = new Commentaire();
        $comment9->setContenu('With a lot of practice, maybe a couple weeks');
        $comment9->setDateCreation($date->add(new \DateInterval('P3DT18H23M')));
        $comment9->setFigure($this->getReference('figure10'));
        $comment9->setAuteur($this->getReference('user2'));
        $manager->persist($comment9);

        $comment10 = new Commentaire();
        $comment10->setContenu("IZI");
        $comment10->setDateCreation($date->add(new \DateInterval('P3D')));
        $comment10->setFigure($this->getReference('figure10'));
        $comment10->setAuteur($this->getReference('user1'));
        $manager->persist($comment10);

        $comment11 = new Commentaire();
        $comment11->setContenu("Franchement, celle là il faut que je la travaille pendant les vacances");
        $comment11->setDateCreation($date->add(new \DateInterval('P3DT2H55M')));
        $comment11->setFigure($this->getReference('figure10'));
        $comment11->setAuteur($this->getReference('user3'));
        $manager->persist($comment11);

        for($i = 0; $i<=10; $i++)
        {
            $comment = new Commentaire();
            $comment->setContenu($i." 
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc iaculis congue fringilla. Sed pellentesque nisi ipsum, vel lacinia ipsum vehicula ut. Cras bibendum sagittis tellus in malesuada. In nec lectus rutrum, semper nulla quis, tempus lectus. Maecenas sit amet fringilla lorem, in vestibulum libero. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas nunc felis, sodales ac lacinia et, euismod et ipsum.");
            $comment->setDateCreation($date);
            $comment->setFigure($this->getReference('figure1'));
            $comment->setAuteur($this->getReference('user1'));
            $manager->persist($comment);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            FigureFixtures::class,
            UtilisateurFixtures::class,

        );
    }
}