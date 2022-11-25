<?php

namespace App\DataFixtures;

use App\Entity\Commentaire;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Figure;
use App\Entity\Groupe;

class FigureFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        for($k=1; $k<=3; $k++)
        {
            $groupe = new Groupe();

            if($k === 1){
                $groupe->setTitre('facile');
            } elseif($k===2){
                $groupe->setTitre('moyen');
            } elseif ($k===3){
                $groupe->setTitre('difficile');
            }
            $manager->persist($groupe);
    
            for($i = 1; $i<= mt_rand(2,4); $i++){
                $figure = new Figure();

                $figure ->setNom("Nom de la Figure n°$i")
                        ->setDescription("<p>Description de la figure n°$i</p>")
                        ->setDateCreation(new \DateTime())
                        ->setDateModification(new \DateTime())
                        ->setGroupe($groupe);

                $manager->persist($figure);
    
                for($j = 1; $j<= mt_rand(3,10); $j++) {
                    $commentaire = new Commentaire();
                    $commentaire    ->setContenu("<p>Contenu n°$j du commentaire pour la figure $i</p>")
                                    ->setDateCreation(new \DateTime())
                                    ->setFigure($figure);
                    $manager->persist($commentaire);
                }
            }
            $manager->flush();
        }
    }
}
