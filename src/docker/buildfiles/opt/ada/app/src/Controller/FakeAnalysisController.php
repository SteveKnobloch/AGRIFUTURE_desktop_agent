<?php

namespace App\Controller;

use App\Entity\Analysis;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FakeAnalysisController extends AbstractController
{
    private string $runUuid;
    public function __construct(private ManagerRegistry $doctrine)
    {
        $runUuid = $_ENV['ADA_RUN_UUID'] ?? null;
        // @todo: if empty($runUuid) throw excpetion
        $this->runUuid = $runUuid;
    }

    #[Route('/fake/analysis/start', name: 'app_fake_analysis_start')]
    public function start(): Response
    {
        // @todo: register and fetch pipeline id from the cloud service

        $analysis = $this->doctrine->getRepository(Analysis::class)->findOneByLocalUuid($this->runUuid);
        if ($analysis) {
            // There can be only one analysis for the uuid
            return $this->forward(IndexController::class . '::index', []);
        }

        $analysis = new Analysis();
        $analysis->setRemotePipelineId(12345);
        $analysis->setName('name from registration form');
        $analysis->setLocalUuid($this->runUuid);
        $analysis->setName('name from upload field');
        $analysis->setStatus(false);
        $analysis->setRelativeDataPath('relative path from registration form');
        $analysis->setFileType('filetype from registration form');
        $this->doctrine->getManager()->persist($analysis);
        $this->doctrine->getManager()->flush();

        return $this->forward(IndexController::class . '::index', []);
    }

    #[Route('/fake/analysis/pause', name: 'app_fake_analysis_pause')]
    public function pause(): Response
    {
        // @todo: send "pause" state to the cloud service
        $analysis = $this->doctrine->getRepository(Analysis::class)->findOneByLocalUuid($this->runUuid);
        $analysis->setPaused(true);
        $this->doctrine->getManager()->flush();

        return $this->forward(IndexController::class . '::index', []);
    }

    #[Route('/fake/analysis/unpause', name: 'app_fake_analysis_unpause')]
    public function unpause(): Response
    {
        // @todo: send "running" state to the cloud service
        $analysis = $this->doctrine->getRepository(Analysis::class)->findOneByLocalUuid($this->runUuid);
        $analysis->setPaused(false);
        $this->doctrine->getManager()->flush();

        return $this->forward(IndexController::class . '::index', []);
    }

    #[Route('/fake/analysis/cancle', name: 'app_fake_analysis_cancle')]
    public function cancle(): Response
    {
        // @todo: send "cancle" state to the cloud service
        $analysis = $this->doctrine->getRepository(Analysis::class)->findOneByLocalUuid($this->runUuid);
        $entityManager = $this->doctrine->getManager();
        $entityManager->remove($analysis);
        $entityManager->flush();

        return $this->forward(IndexController::class . '::index', []);
    }

    #[Route('/fake/analysis/finish', name: 'app_fake_analysis_finish')]
    public function finish(): Response
    {
        // @todo: send "finish" state to the cloud service
        $analysis = $this->doctrine->getRepository(Analysis::class)->findOneByLocalUuid($this->runUuid);
        $entityManager = $this->doctrine->getManager();
        $entityManager->remove($analysis);
        $entityManager->flush();

        return $this->forward(IndexController::class . '::index', []);
    }
}
