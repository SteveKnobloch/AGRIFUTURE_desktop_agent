<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Entity\Analysis;
use App\Entity\RemoteAnalysis;
use App\Entity\Token;
use App\Enum\AnalysisStatus;
use App\Enum\CreateAnalysisError;
use App\Enum\GetAnalysisError;
use App\Enum\UpdateStatusError;
use App\Form\AnalysisForm;
use App\Form\Entity\UpdateStatus;
use App\Form\UpdateStatusType;
use App\Repository\AnalysisRepository;
use App\Repository\UploadRepository;
use App\Service\ApiService;
use App\Service\CurrentAnalysisFactory;
use Doctrine\Common\Collections\Criteria;
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
        public readonly UploadRepository $uploads,
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
                    [
                        '_locale' => $_locale,
                        ...$request->attributes->all(),
                    ]
                );
            }

            if ($analysis === GetAnalysisError::Forbidden) {
                $account = $this->api->getTokenInformation($_locale);
                if ($account instanceof Token) {
                    return $this->forward(
                        ErrorController::class . '::forbidden',
                        [
                            ...$request->attributes->all(),
                        ]
                    );
                }

                return $this->forward(
                    ErrorController::class . '::apiAccessForbidden',
                    [
                        '_locale' => $_locale,
                        ...$request->attributes->all(),
                    ]
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
                    [
                        '_locale' => $_locale,
                        ...$request->attributes->all(),
                    ]
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
            self::addUploadErrors(
                $this,
                $analysisFactory,
                $analysis,
                $i18n
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

        self::addUploadErrors(
            $this,
            $analysisFactory,
            $analysis,
            $i18n
        );

        $params = array_map(
            fn(FormInterface $form) => $form->createView(),
            $actions
        );

        $params['currentFile'] = $this->getCurrentFile();

        return $this->render(
            'pages/analysis/show.html.twig',
            $params,
        );
    }

    private function getCurrentFile(): string
    {
        $upload = $this->uploads->findBy(
            ['analysis' => $this->analyses->current()->localUuid],
            ['id' => Criteria::DESC],
        )[0];

        $parts = explode('/', $upload->fileName);

        return array_pop($parts);
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
                    'label' => 'Finish analysis',
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
                            [
                                '_locale' => $request->getLocale(),
                                ...$request->attributes->all(),
                            ]
                        );
                    }

                    if ($analysis === GetAnalysisError::Forbidden) {
                        return $this->forward(
                            ErrorController::class . '::apiAccessForbidden',
                            [
                                '_locale' => $request->getLocale(),
                                ...$request->attributes->all(),
                            ]
                        );
                    }

                    if ($analysis === GetAnalysisError::UnknownError &&
                        !$this->api->checkInternetConnectivity(
                            $request->getLocale()
                        )
                    ) {
                        return $this->forward(
                            ErrorController::class . '::offline',
                            [
                                '_locale' => $request->getLocale(),
                                ...$request->attributes->all(),
                            ]
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

    public static function addUploadErrors(
        AbstractController $controller,
        CurrentAnalysisFactory $analysisFactory,
        ?RemoteAnalysis $analysis,
        TranslatorInterface $i18n,
    ): void {
        $localFiles = [];

        // Add upload errors and warnings
        foreach ($analysisFactory->cached()->getUploads() as $upload) {
            if ($upload->getError()) {
                $controller->addFlash(
                    $upload->isUploaded() ? 'warning' : 'danger',
                    $i18n->trans(
                        $i18n->trans("uploadError.{$upload->getError()->name}"),
                        [
                            '{file}' => $upload->fileName,
                        ]
                    )
                );
                continue;
            }

            $baseName = basename($upload->fileName);
            $isGz = str_ends_with($baseName, '.gz');
            if ($isGz) {
                $baseName = substr(
                    $baseName,
                    0,
                    strlen($baseName) - 3
                );
            }
            $name = substr(
                $baseName,
                0,
                strrpos($baseName, '.')
            );

            $localFiles[$name] = [
                'exists' => fn() => is_file($upload->fileName),
                'size' => fn() => filesize($upload->fileName),
                'crc32' => fn() => crc32($upload->fileName),
                'gz' => $isGz
            ];
        }

        $warning = fn() => $i18n->trans(
            'The files in the upload folder seem to differ from the uploaded ' .
            'ones. If you haven’t touched them on disk, your analysis may be ' .
            'inaccurate.'
        );
        foreach (($analysis?->getUploaded() ?? []) as $remoteUpload) {
            if (!isset($localFiles[$remoteUpload->fileName])) {
                $controller->addFlash(
                    'warning', $warning()
                );
                break;
            }

            $local = $localFiles[$remoteUpload->fileName];

            if (!$local['exists']()) {
                $controller->addFlash(
                    'warning', $warning()
                );
                break;
            }

            if ($local['gz']) {
                continue;
            }

            if ($remoteUpload->size &&
                $remoteUpload->size !== $local['size']()
            ) {
                $controller->addFlash(
                    'warning', $warning()
                );
                break;
            }

            // CRC seems to differ even for correct uploads.
        }
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
                    [
                        '_locale' => $_locale,
                        ...$request->attributes->all(),
                    ]
                );
            }

            if ($result === CreateAnalysisError::ApiAccessForbidden) {
                return $this->forward(
                    ErrorController::class . '::apiAccessForbidden',
                    [
                        '_locale' => $_locale,
                        ...$request->attributes->all(),
                    ]
                );
            }

            if ($result === CreateAnalysisError::UnknownError &&
                !$this->api->checkInternetConnectivity($_locale)
            ) {
                return $this->forward(
                    ErrorController::class . '::offline',
                    [
                        '_locale' => $_locale,
                        ...$request->attributes->all(),
                    ]
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
