<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use App\Repository\CommentaireRepository;
use App\Repository\FigureRepository;

use App\Entity\Commentaire;
use App\Entity\Figure;
use App\Form\CommentaireType;
use App\Entity\Images;
use App\Entity\Video;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[Route('/snowtricks', name: 'app_home')]
    public function index (FigureRepository $repo, Request $request): Response
    {
        $figures = $repo->findFiguresPaginated($request->query->getInt('limit', 15));

        return $this->render('website/index.html.twig', [
            'controller_name' => 'HomeController',
            'figures' => $figures
        ]);
    }

    #[Route('/snowtricks/figure/show/{id}', name: 'app_figure_show')]
    public function show(Figure $figure, CommentaireRepository $repo, Request $request,ManagerRegistry $doctrine)
    {        
        $commentaire = new Commentaire();

        $form = $this->createForm(CommentaireType::class, $commentaire);

        $form->handleRequest($request);

        $manager = $doctrine->getManager();

        $commentaires = $repo->findCommentairesPaginated($figure->getId(),$request->query->getInt('limit', 10));

        if($form->isSubmitted() && $form->isValid()){

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

        $filesystem = new Filesystem();

        foreach($imagesNom as $nomImage){
            $filesystem->remove($this->getParameter('images_directory').'/'.$nomImage['nom']);
        }

        $entityManager->remove($figure);
        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }
}
