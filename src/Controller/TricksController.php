<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Form\FigureType;
use App\Entity\Figure;
use App\Entity\Images;
use App\Entity\Video;

class TricksController extends AbstractController
{
    #[Route('/snowtricks/figure/new', name: 'app_figure_create')]
    public function createNewFigure(Request $request, ManagerRegistry $doctrine)
    {
        $figure = new Figure();

        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        $entityManager = $doctrine->getManager();

        if($form->isSubmitted() && $form->isValid()) {
            $figure->setDateCreation(new \DateTime());
            $figure->setDateModification(new \DateTime());
            
            // Gestion des images
            $images = $form->get('images')->getData();

            if(!$images) {
                $defaultImage ='default.jpg';
                $default = new Images();
                $default->setNom($defaultImage);
                // $entityManager->persist($figure);
                //Création de l'image en db
                $entityManager->persist($default);
                $figure->addImage($default);
            } else {
                foreach($images as $image){
                    //Gestion du nom du fichier
                    $fichier = md5(uniqid()).'.'.$image->guessExtension();
                    //Copie du fichier dans le dossier uploads
                    $image->move(
                        $this->getParameter('images_directory'),
                        $fichier
                    );
                    // Création de l'image en db
                    $img = new Images();
                    $img->setNom($fichier);
                    $entityManager->persist($img);
                    $figure->addImage($img);
                }
            }

            // Upload des videos en db
            $videos = $form->get('video')->getData();

            if($videos != null){
                $video = new Video;
                $video->setUrl($videos);
                $entityManager->persist($video);
                $figure->addVideo($video);
            }
            $entityManager->persist($figure);
            $entityManager->flush();
    
            $this->addFlash('success', 'Votre figure à bien été enregistrée');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('website/figureCreate.html.twig', [
            'formNewFigure' => $form->createView(),
            'figure' => $figure
        ]);
    }

    #[Route('/snowtricks/figure/edit/{id}', name: 'app_figure_edit')]
    public function editNewFigure(Figure $figure = null, Request $request, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $figure->setDateModification(new \DateTime());
            // Gestion des images
            $images = $form->get('images')->getData();
            foreach($images as $image){
                // Gestion du nom du fichier
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                // Création de l'image en db
                $img = new Images();
                $img->setNom($fichier);
                $entityManager->persist($img);
                $figure->addImage($img);
            }

            // Upload des videos en db
            $videos = $form->get('video')->getData();
            if($videos != null){
                $video = new Video;
                $video->setUrl($videos);
                $entityManager->persist($video);
                $figure->addVideo($video);
            }
            
            $entityManager->persist($figure);
            $entityManager->flush();

            $this->addFlash('success', 'Votre figure à bien été enregistrée');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('website/figureEdit.html.twig', [
            'formEditFigure' =>$form->createView(),
            'figure' => $figure
        ]);
    }
}
