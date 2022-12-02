<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Entity\Analysis;
use App\Enum\CreateAnalysisError;
use App\Form\AnalysisForm;
use App\Repository\AnalysisRepository;
use App\Service\ApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AnalysisController extends AbstractController
{
    public function __construct(
        public readonly ApiService $api,
        public readonly AnalysisRepository $analyses,
    ) {}

    #[Route(
        path: '/{_locale}/analysis',
        name: 'app_page_analysis_show',
        requirements: [
            '_locale' => 'en|de',
        ],
    )]
    public function analysis(): Response
    {
        return $this->render('pages/analysis/show.html.twig');
    }

    #[Route(
        path: '/{_locale}/analysis/register',
        name: 'app_page_analysis_register',
        requirements: [
            '_locale' => 'en|de',
        ],
    )]
    public function registerAnalysis(
        Request $request,
        string $_locale,
        TranslatorInterface $i18n,
    ): Response
    {
        if ($this->analyses->current()) {
            return $this->redirectToRoute('app_page_analysis_show');
        }

        $form = $this->createForm(
            AnalysisForm::class,
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->api->createAnalysis(
                $_locale,
                $form->getData(),
            );

            if ($result instanceof Analysis) {
                $this->analyses->save($result, true);
                return $this->redirectToRoute('app_page_analysis_show');
            }

            assert($result instanceof CreateAnalysisError);

            if ($result === CreateAnalysisError::InvalidToken) {
                return $this->forward(ErrorController::class . '::invalidToken');
            }

            if ($result === CreateAnalysisError::ApiAccessForbidden) {
                return $this->forward(ErrorController::class . '::apiAccessForbidden');
            }

            if ($result === CreateAnalysisError::UnknownError &&
                !$this->api->checkInternetConnectivity($_locale)
            ) {
                return $this->forward(ErrorController::class . '::offline');
            }

            $form->addError(
                new FormError($i18n->trans($result->name))
            );
        }

        return $this->render(
            'pages/analysis/register.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
