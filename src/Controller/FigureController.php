<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem as FilesystemFilesystem;

use App\Form\FigureType;
use App\Entity\Figure;
use App\Entity\Images;
use App\Entity\Video;

class FigureController extends AbstractController
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

    /**
     * Suppression des images
     */
    #[Route('/snowtricks/figure/{id}/image/delete', name: 'app_figure_image_delete',  methods:["DELETE", "GET"])]
    public function deleteImage(Images $image,ManagerRegistry $doctrine)
    {

        $figureId = $image->getFigure()->getId();

        $nomImg = $image->getNom();
        // On unlink le fichier
        unlink($this->getParameter('images_directory').'/'.$nomImg);

        // On supprime l'entrée en db
        $entityManager = $doctrine->getManager();
        $entityManager->remove($image);
        $entityManager->flush();

        return $this->redirectToRoute('app_figure_edit', ['id' => $figureId]);
    }

    /**
     * Suppression des videos
     */
    #[Route('/snowtricks/figure/{id}/video/delete', name: 'app_figure_video_delete', methods:["DELETE", "GET"])]
    public function deleteVideo(Video $video,ManagerRegistry $doctrine)
    {

        $figureId = $video->getFigure()->getId();

        // On supprime l'entrée en db
        $entityManager = $doctrine->getManager();
        $entityManager->remove($video);
        $entityManager->flush();

        return $this->redirectToRoute('app_figure_edit', ['id' => $figureId]);
    }

    /**
     * Suppression de la figure
     */
    #[Route('/snowtricks/figure/{id}/delete', name: 'app_figure_delete')]
    public function deleteFigure(Figure $figure, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();

        // Supression des images dans le file
        // On cherche d'abord à récupérer les noms des images
        $figureId = $figure->getId();
        $repo = $entityManager->getRepository(Images::class);
        $imagesNom = $repo->findNameByFigureId($figureId);

        $filesystem = new FilesystemFilesystem();

        foreach($imagesNom as $nomImage){
            $filesystem->remove($this->getParameter('images_directory').'/'.$nomImage['nom']);
        }

        $entityManager->remove($figure);
        $entityManager->flush();

        return $this->redirectToRoute('app_home');

    }
}
