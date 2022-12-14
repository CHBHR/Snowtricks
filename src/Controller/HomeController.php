<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

use App\Repository\FigureRepository;
use App\Entity\Commentaire;
use App\Entity\Figure;
use App\Form\CommentaireType;
use App\Form\FigureType;
use App\Entity\Images;
use App\Entity\Video;
use App\Repository\CommentaireRepository;
use App\Repository\ImagesRepository;
use Symfony\Component\Filesystem\Filesystem;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index (FigureRepository $repo, Request $request): Response
    {
        $figures = $repo->findFiguresPaginated($request->query->getInt('limit', 15));

        return $this->render('website/index.html.twig', [
            'controller_name' => 'HomeController',
            'figures' => $figures,
        ]);
    }

    /**
     * L'ordre de déclaration des routes permet de ne pas avoir d'erreur lors de l'appel si plusieurs routes ont des noms similaires
     */
    #[Route('/figure/new', name: 'app_figure_create')]
    #[Route('/figure/{id}/edit', name: 'app_figure_edit')]
    public function formFigure(Figure $figure = null, Request $request, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();

        if(!$figure){
            $figure = new Figure();
        }

        /**
         * Créé le formulaire basé sur les informations de l'entitée
         * le formulaire à été crée avec la cli et évite la duplication du code 
         */
        $form = $this->createForm(FigureType::class, $figure);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if($figure->getId() === FALSE){
                $figure->setDateCreation(new \DateTime());
                $figure->setDateModification(new \DateTime());
            }
            $figure->setDateModification(new \DateTime());

            /**
             * Gestion de l'upload des images
             */
            $images = $form->get('images')->getData();
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

            return $this->redirectToRoute('app_figure_show', ['id' => $figure->getId()]);
        }

        /**
         * $form étant un object complex, passe le résultat de la fonction createView() à twig pour qu'il puisse le traiter
        */
        return $this->render('website/figureEdit.html.twig', [
            'formNewFigure' => $form->createView(),
            'formEditFigure' => $figure->getId() !== null,
            'figure' => $figure,
        ]);
    }

    #[Route('/figure/{id}', name: 'app_figure_show')]
    public function show(Figure $figure, CommentaireRepository $repo, Request $request,ManagerRegistry $doctrine)
    {        
        $commentaire = new Commentaire();

        $form = $this->createForm(CommentaireType::class, $commentaire);

        $form->handleRequest($request);

        $manager = $doctrine->getManager();

        $commentaires = $repo->findCommentairesPaginated($figure->getId(),$request->query->getInt('limit', 10));

        if($form->isSubmitted() && $form->isValid()){

            /** 
             * @var \App\Entity\Utilisateur $auteur 
             * 
             * */
            $auteur = $this->getUser();

            $commentaire    ->setDateCreation(new \DateTime())
                            ->setFigure($figure)
                            ->setAuteur($auteur);

            $manager->persist($commentaire);
            $manager->flush();

            return $this->redirectToRoute('app_figure_show', [
                'id' => $figure->getId()
            ]);
        }

        return $this->render('website/show.html.twig', [
            'figure' => $figure,
            'commentaires' => $commentaires,
            'formCommentaire' =>$form->createView()
        ]);
    }

    /**
     * Suppression des images
     */
    #[Route('/figure/{id}/image/delete', name: 'app_figure_image_delete',  methods:["DELETE", "GET"])]
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
    #[Route('/figure/{id}/video/delete', name: 'app_figure_video_delete', methods:["DELETE", "GET"])]
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
    #[Route('/figure/{id}/delete', name: 'app_figure_delete')]
    public function deleteFigure(Figure $figure, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();

        // Supression des images dans le file
        // On cherche d'abord à récupérer les noms des images
        $figureId = $figure->getId();
        $repo = $entityManager->getRepository(Images::class);
        $imagesNom = $repo->findNameByFigureId($figureId);

        $filesystem = new Filesystem();

        foreach($imagesNom as $nomImage){
            $filesystem->remove($this->getParameter('images_directory').'/'.$nomImage['nom']);
        }

        $entityManager->remove($figure);
        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }
}
