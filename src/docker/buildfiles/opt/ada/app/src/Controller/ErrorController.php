<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Enum\AnalysisStatus;
use App\Form\UpdateStatusType;
use App\Repository\AnalysisRepository;
use App\Service\ApiService;
use App\Service\CurrentAnalysisFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ErrorController extends AbstractController
{
    #[Route(
        path: '/{_locale}/error/invalid-token',
        name: 'app_error_invalid_token',
        requirements: [
            '_locale' => 'en|de',
        ],
    )]
    public function invalidToken(
        ApiService $apiService,
        string $_locale,
    ): Response
    {
        if (!$apiService->checkInternetConnectivity($_locale)) {
            $this->forward(self::class . '::offline');
        }

        return $this->render(
            'pages/error/invalid_token.html.twig',
        );
    }

    #[Route(
        path: '/{_locale}/error/api-access-forbidden',
        name: 'app_error_api_access_forbidden',
        requirements: [
            '_locale' => 'en|de',
        ],
    )]
    public function apiAccessForbidden(
        ApiService $apiService,
        string $_locale,
    ): Response {
        if (!$apiService->checkInternetConnectivity($_locale)) {
            $this->forward(self::class . '::offline');
        }

        return $this->render(
            'pages/error/api_access_forbidden.html.twig',
        );
    }

    #[Route(
        path: '/{_locale}/error/forbidden',
        name: 'app_error_forbidden',
        requirements: [
            '_locale' => 'en|de',
        ],
    )]
    public function forbidden(
        ApiService $apiService,
    ): Response
    {
        return $this->render(
            'pages/error/forbidden.html.twig',
            [
                'abandon' => $this->abandonForm()->createView(),
            ]
        );
    }
    #[Route(
        path: '/{_locale}/analysis/abandon',
        name: 'app_page_analysis_abandon',
        requirements: [
            '_locale' => 'en|de',
        ],
    )]
    public function abandonAnalysis(
        Request $request,
        AnalysisRepository $analyses,
        CurrentAnalysisFactory $analysisFactory,
        TranslatorInterface $i18n,
    ) {
        $form = $this->abandonForm();
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->addFlash(
                'danger',
                $i18n->trans(
                    'The CSRF token is invalid. Please try to resubmit the form.',
                    domain: 'validators'
                )
            );
            return $this->redirectToRoute('app_page_analysis_show');
        }

        $current = $analysisFactory->cached();
        if ($current) {
            AnalysisController::addUploadErrors(
                $this,
                $analysisFactory,
                $analysisFactory(),
                $i18n
            );

            $analyses->remove($current, true);
            $this->addFlash(
                'warning',
                $i18n->trans('The analysis was abandoned. You can still check it in the portal. Please consider finishing or canceling it.')
            );
        }

        return $this->redirectToRoute(
            'app_page_analysis_register',
        );
    }

    public function abandonForm(): FormInterface
    {
        return $this->createForm(
            UpdateStatusType::class,
            options: [
                'status' => AnalysisStatus::unknown,
                'label' => 'Abandon',
                'class' => 'btn btn-danger',
                'icon' => 'x-octagon-fill',
                'action' => 'app_page_analysis_abandon'
            ]
        );
    }

    #[Route(
        path: '/{_locale}/error/offline',
        name: 'app_error_offline',
        requirements: [
            '_locale' => 'en|de',
        ],
    )]
    public function offline(string $_locale): Response
    {
        return $this->render(
            'pages/error/offline.html.twig',
        );
    }
}
