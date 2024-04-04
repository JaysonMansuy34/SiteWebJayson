<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        return $this->render('home/accueil.html.twig');
    }

    #[Route('/parcours/jayson', name: 'parcourspage')]
    public function parcours(): Response
    {
        return $this->render('home/detailsProfile.html.twig');
    }
}
