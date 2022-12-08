<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Entity\Analysis;
use App\Enum\AnalysisStatus;
use App\Enum\CreateAnalysisError;
use App\Enum\GetAnalysisError;
use App\Enum\UpdateStatusError;
use App\Form\AnalysisForm;
use App\Form\Entity\UpdateStatus;
use App\Form\UpdateStatusType;
use App\Repository\AnalysisRepository;
use App\Service\ApiService;
use App\Service\CurrentAnalysisFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Error\RuntimeError;

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
    public function analysis(
        Request $request,
        CurrentAnalysisFactory $analysisFactory,
        TranslatorInterface $i18n,
        string $_locale,
    ): Response
    {
        if (!$analysisFactory->isRegistered()) {
            return $this->redirectToRoute('app_page_analysis_register');
        }

        $analysis = $analysisFactory->forceFetch();

        if ($analysis instanceof GetAnalysisError) {
            if ($analysis === GetAnalysisError::InvalidToken) {
                return $this->forward(
                    ErrorController::class . '::invalidToken',
                    ['_locale' => $_locale]
                );
            }

            if ($analysis === GetAnalysisError::ApiAccessForbidden) {
                return $this->forward(
                    ErrorController::class . '::apiAccessForbidden',
                    ['_locale' => $_locale]
                );
            }

            if ($analysis === GetAnalysisError::NoSuchAnalysis) {
                // Edge case when the API forgets the analysis exists.
                $this->analyses->remove($analysisFactory->cached(), true);
                return $this->redirectToRoute('app_page_analysis_register');
            }

            if ($analysis === GetAnalysisError::UnknownError &&
                !$this->api->checkInternetConnectivity($_locale)
            ) {
                return $this->forward(
                    ErrorController::class . '::offline',
                    ['_locale' => $_locale]
                );
            }

            throw new RuntimeError('Fetching analysis failed.');
        }

        if ($analysis->getStatus()->isFinished()) {
            $this->addFlash(
                $analysis->getStatus() === AnalysisStatus::completed ?
                    'success' :
                    'danger',
                $analysis->getFinishedReason()
            );

            $this->analyses->remove($analysisFactory->cached(), true);
            return $this->redirectToRoute('app_page_analysis_register');
        }

        $actions = $this->analysisActions(
            $request,
            $analysisFactory->cached(),
            $i18n,
        );

        if ($actions instanceof Response) {
            return $actions;
        }

        return $this->render(
            'pages/analysis/show.html.twig',
            array_map(
                fn(FormInterface $form) => $form->createView(),
                $actions
            )
        );
    }

    private function analysisActions(
        Request $request,
        Analysis $analysis,
        TranslatorInterface $i18n,
    ): Response|array {
        /** @var array<string, FormInterface> $forms */
        $forms = [
            'resume' => $this->createForm(
                UpdateStatusType::class,
                options: [
                    'status' => AnalysisStatus::running,
                    'label' => 'Continue',
                    'class' => 'btn btn-primary',
                    'icon' => 'play-circle'
                ]
            ),
            'pause' => $this->createForm(
                UpdateStatusType::class,
                options: [
                    'status' => AnalysisStatus::paused,
                    'label' => 'Pause',
                    'class' => 'btn btn-primary',
                    'icon' => 'pause-circle'
                ]
            ),
            'finish' => $this->createForm(
                UpdateStatusType::class,
                options: [
                    'status' => AnalysisStatus::completed,
                    'label' => 'Finish analaysis',
                    'class' => 'btn btn-primary',
                ]
            ),
            'cancel' => $this->createForm(
                UpdateStatusType::class,
                options: [
                    'status' => AnalysisStatus::crashed,
                    'label' => 'Cancel analysis',
                    'class' => 'btn btn-danger',
                ]
            ),
        ];

        foreach ($forms as $form) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $newStatus = $form->getData();
                assert($newStatus instanceof UpdateStatus);

                $updated = $this->api->updateStatus(
                    $request->getLocale(),
                    $analysis,
                    $newStatus->getStatus(),
                );

                if ($updated instanceof UpdateStatusError) {
                    if ($analysis === GetAnalysisError::InvalidToken) {
                        return $this->forward(
                            ErrorController::class . '::invalidToken',
                            ['_locale' => $request->getLocale()]
                        );
                    }

                    if ($analysis === GetAnalysisError::ApiAccessForbidden) {
                        return $this->forward(
                            ErrorController::class . '::apiAccessForbidden',
                            ['_locale' => $request->getLocale()]
                        );
                    }

                    if ($analysis === GetAnalysisError::UnknownError &&
                        !$this->api->checkInternetConnectivity(
                            $request->getLocale()
                        )
                    ) {
                        return $this->forward(
                            ErrorController::class . '::offline',
                            ['_locale' => $request->getLocale()]
                        );
                    }

                    $this->addFlash(
                        'danger',
                        $i18n->trans(
                            UpdateStatusError::UnknownError->name
                        ),
                    );

                    return $forms;
                }

                $analysis->setStatus($updated->getStatus());
                $this->analyses->save($analysis, true);

                // We have already loaded some data which would be tricky to
                // reset - we reload the site in order to get a clean slate.
                return $this->redirectToRoute('app_page_analysis_show');
            }
        }

        return $forms;
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
                return $this->forward(
                    ErrorController::class . '::invalidToken',
                    ['_locale' => $_locale]
                );
            }

            if ($result === CreateAnalysisError::ApiAccessForbidden) {
                return $this->forward(
                    ErrorController::class . '::apiAccessForbidden',
                    ['_locale' => $_locale]
                );
            }

            if ($result === CreateAnalysisError::UnknownError &&
                !$this->api->checkInternetConnectivity($_locale)
            ) {
                return $this->forward(
                    ErrorController::class . '::offline',
                    ['_locale' => $_locale]
                );
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
