<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Figure;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;
use App\Repository\FigureRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[Route('/snowtricks', name: 'app_home')]
    public function index(FigureRepository $repo, Request $request): Response
    {
        $figures = $repo->findFiguresPaginated($request->query->getInt('limit', 15));

        return $this->render('website/index.html.twig', [
            'controller_name' => 'HomeController',
            'figures' => $figures,
        ]);
    }

    #[Route('/snowtricks/show/{nom}', name: 'app_figure_show')]
    public function show(Figure $figure, CommentaireRepository $repo, Request $request, ManagerRegistry $doctrine)
    {
        $commentaire = new Commentaire();

        $form = $this->createForm(CommentaireType::class, $commentaire);

        $form->handleRequest($request);

        $manager = $doctrine->getManager();

        $commentaires = $repo->findCommentairesPaginated($figure->getId(), $request->query->getInt('limit', 10));

        if ($form->isSubmitted() && $form->isValid()) {
            $auteur = $this->getUser();

            $commentaire->setDateCreation(new \DateTime())
                            ->setFigure($figure)
                            ->setAuteur($auteur);

            $manager->persist($commentaire);
            $manager->flush();

            return $this->redirectToRoute('app_figure_show', [
                'id' => $figure->getId(),
            ]);
        }

        return $this->render('website/show.html.twig', [
            'figure' => $figure,
            'commentaires' => $commentaires,
            'formCommentaire' => $form->createView(),
        ]);
    }
}
