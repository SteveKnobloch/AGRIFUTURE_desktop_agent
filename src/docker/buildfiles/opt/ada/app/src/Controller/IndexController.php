<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route(
        path: '/{_locale}',
        name: 'app_index',
        requirements: [
            '_locale' => 'en|de',
        ],
    )]
    public function index(): Response
    {
        return $this->render('index.html.twig', []);
    }
}
