<?php

namespace App\Command;

use App\Entity\Analysis;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:cancle-analysis',
    description: 'Cancle a analysis by the desktop agent run uuid. Acts as a safety net when the desktop agent is closed. Usually called by the launcher script at shutdown.',
)]
class CancleAnalysisCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('runUuid', InputArgument::REQUIRED, 'The desktop agent run uuid')
        ;
    }

    public function __construct(private ManagerRegistry $doctrine)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $runUuid = $input->getArgument('runUuid');
        $analysis = $this->doctrine->getRepository(Analysis::class)->findOneByLocalUuid($runUuid);
        if ($analysis) {
            // @todo: Send "cancled" to the cloud service.
            // @todo: If the cloud service detects that the analysis has already been completed (or canceled), the cloud service takes no action.

            $entityManager = $this->doctrine->getManager();
            $entityManager->remove($analysis);
            $entityManager->flush();

            $io->success(sprintf('Analysis deleted (%s)', $runUuid));
        } else {
            $io->note(sprintf('No Analysis to delete (%s)', $runUuid));
        }

        return Command::SUCCESS;
    }
}
