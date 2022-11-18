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

    // @toDo
    // following routes are only necessary for the FE development process
    // can be removed after FE development

    #[Route(
        path: '/{_locale}/analysis',
        name: 'app_page_analysis',
        requirements: [
            '_locale' => 'en|de',
        ],
    )]
    public function analysis(): Response
    {
        return $this->render('pages/analysis.html.twig', []);
    }
    #[Route(
        path: '/{_locale}/analysis/register',
        name: 'app_page_analysis_register',
        requirements: [
            '_locale' => 'en|de',
        ],
    )]
    public function analysis_register(): Response
    {
        return $this->render('pages/analysis_register.html.twig', []);
    }
}
