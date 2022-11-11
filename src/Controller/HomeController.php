<?php

namespace App\Controller;

use App\Entity\Figure;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\FigureRepository;

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

    #[Route('/figure/{id}', name: 'app_show')]
    public function show(Figure $figure)
    {
        return $this->render('website/show.html.twig', [
            'figure' => $figure,
        ]);
    }

}
