<?php

namespace App\Command;

use App\Entity\Analysis;
use App\Entity\RemoteAnalysis;
use App\Enum\AnalysisStatus;
use App\Repository\AnalysisRepository;
use App\Service\ApiService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:cancel-analysis',
    description: 'Cancel a analysis by the desktop agent run uuid. Acts as a safety net when the desktop agent is closed. Usually called by the launcher script at shutdown.',
)]
class CancelAnalysisCommand extends Command
{
    public function __construct(
        private readonly AnalysisRepository $analyses,
        private readonly ApiService $apiService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $analysis = $this->analyses->current();

        if (!$analysis) {
            if (!$output->isQuiet()) {
                $output->writeln('No such analysis.');
            }
            return 0;
        }

        if (!$analysis->getStatus()->isFinished()) {
            $cancel = $this->apiService->updateStatus(
                'en',
                $analysis,
                AnalysisStatus::crashed
            );

            if (!$cancel instanceof RemoteAnalysis) {
                $io->error(
                    "Canceling analysis {$analysis->localUuid} failed: " .
                    $cancel->name
                );
            }
        }

        $this->analyses->remove($analysis, true);

        return 0;
    }
}
