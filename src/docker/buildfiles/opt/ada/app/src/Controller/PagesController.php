<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends AbstractController
{
    #[Route('/privacy', name: 'app_page_privacy')]
    public function privacy(): Response
    {
        return $this->render('pages/privacy.html.twig', []);
    }

    #[Route('/imprint', name: 'app_page_imprint')]
    public function imprint(): Response
    {
        return $this->render('pages/imprint.html.twig', []);
    }

    #[Route('/information', name: 'app_page_information')]
    public function information(): Response
    {
        return $this->render('pages/information.html.twig', []);
    }
}
