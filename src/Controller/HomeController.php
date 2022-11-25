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

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index (FigureRepository $repo): Response
    {
        $figures = $repo->findAll();

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
    public function formFigure(Figure $figure = null, Request $request, ManagerRegistry $doctrine )
    {
        $entityManager = $doctrine->getManager();

        if(!$figure){
            $figure = new Figure();
        }

        /**
         * créé le formulaire basé sur les informations de l'entitée
         * le formulaire à été crée avec la cli et évite la duplication du code 
         */
        $form = $this->createForm(FigureType::class, $figure);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if(!$figure->getId()){
                $figure->setDateCreation(new \DateTime());
                $figure->setDateModification(new \DateTime());
            }
            $figure->setDateModification(new \DateTime());

            $entityManager->persist($figure);
            $entityManager->flush();

            return $this->redirectToRoute('app_figure_show', ['id' => $figure->getId()]);
        }

        /**
         * $form étant un object complex, passe le résultat de la fonction createView() à twig pour qu'il puisse le traiter
        */
        return $this->render('website/create.html.twig', [
            'formNewFigure' => $form->createView(),
            'formEditFigure' => $figure->getId() !== null
        ]);
    }

    #[Route('/figure/{id}', name: 'app_figure_show')]
    public function show(Figure $figure, Request $request,ManagerRegistry $doctrine)
    {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);

        $form->handleRequest($request);

        $manager = $doctrine->getManager();

        if($form->isSubmitted() && $form->isValid()){
            $commentaire    ->setDateCreation(new \DateTime())
                            ->setFigure($figure);
            
            $manager->persist($commentaire);
            $manager->flush();

            return $this->redirectToRoute('app_figure_show', [
                'id' => $figure->getId()
            ]);
        }

        return $this->render('website/show.html.twig', [
            'figure' => $figure,
            'formCommentaire' =>$form->createView()
        ]);
    }

}
