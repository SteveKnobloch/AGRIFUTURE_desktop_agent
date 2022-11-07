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
    name: 'app:watcher',
    description: 'Observe if an analysis has started and then upload the files to the cloud service.',
)]
class WatcherCommand extends Command
{
    public function __construct(private ManagerRegistry $doctrine)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $runUuid = $_ENV['ADA_RUN_UUID'] ?? null;
        if (!$runUuid) {
            $io->error('No run uuid');
            return Command::FAILURE;
        }

        $repository = $this->doctrine->getRepository(Analysis::class);
        $entityManager = $this->doctrine->getManager();
        while (true) {
            $entityManager->clear();
            $analysis = $repository->findOneByLocalUuid($runUuid);

            if (!$analysis || $analysis->isPaused()) {
                $io->info('Nothing todo');
                sleep(10);
                continue;
            }

            $io->info('Do something');
            sleep(1);
            // @todo: scan files within /data/$analysis->getRelativePath()
            // @todo: filter files that ar already uploaded ($analysis->getUploads())
            // @todo: filter files by extension ($analysis->getFileType())
            // @todo: if the files are compressed ($analysis->getFileType().gz), uncompress to var/
            // @todo: ask cloud service if an upload can be performed
            // @todo: upload to the cloud service
            // @todo: add the file to the analysis ($analysis->addUpload())
        }

        return Command::SUCCESS;
    }
}
