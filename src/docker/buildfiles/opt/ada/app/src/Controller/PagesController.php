<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends AbstractController
{
    #[Route(
        path: '/{_locale}/privacy',
        name: 'app_page_privacy',
        requirements: [
            '_locale' => 'en|de',
        ],
    )]
    public function privacy(): Response
    {
        return $this->render('pages/privacy.html.twig', []);
    }

    #[Route(
        path: '/{_locale}/imprint',
        name: 'app_page_imprint',
        requirements: [
            '_locale' => 'en|de',
        ],
    )]
    public function imprint(): Response
    {
        return $this->render('pages/imprint.html.twig', []);
    }

    #[Route(
        path: '/{_locale}/information',
        name: 'app_page_information',
        requirements: [
            '_locale' => 'en|de',
        ],
    )]
    public function information(): Response
    {
        return $this->render('pages/information.html.twig', []);
    }
}
