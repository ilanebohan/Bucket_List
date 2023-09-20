<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function home(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'monTexte' => 'Bonjour, ceci est un texte de test',
        ]);
    }


    #[Route('/aboutUs', name: 'about_us')]
    public function aboutUS(): Response
    {
        return $this->render('main/aboutus.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
