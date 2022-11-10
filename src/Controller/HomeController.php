<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('website/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/figure/12', name: 'app_show')]
    public function show()
    {
        return $this->render('website/show.html.twig');
    }

}
