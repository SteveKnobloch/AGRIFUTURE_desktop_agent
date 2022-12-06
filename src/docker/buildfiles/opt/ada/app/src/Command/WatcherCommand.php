<?php

namespace App\Command;

use App\Entity\Analysis;
use App\Entity\Upload;
use App\Enum\UploadFileError;
use App\Repository\AnalysisRepository;
use App\Repository\UploadRepository;
use App\Service\ApiService;
use App\Service\UploadService;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:watcher',
    description: 'Observe if an analysis has started and then upload the files to the cloud service.',
)]
class WatcherCommand extends Command
{
    public function __construct(
        private readonly AnalysisRepository $analyses,
        private readonly UploadRepository $uploads,
        private readonly UploadService $uploadService,
        private readonly ApiService $api,
        private readonly ManagerRegistry $persistence,
    ) {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): never {
        while (true) {
            if (isset($analysis)) {
                $this->persistence->getManager()->clear();
                sleep(10);
            }

            $analysis = $this->analyses->current();

            if (!$analysis->getStatus()->shouldUploadFiles()) {
                if (!$output->isQuiet()) {
                    $output->writeln('No analysis running.');
                }
                sleep(1);
                continue;
            }

            if (!$output->isQuiet()) {
                $output->write(
                    "Uploading for analysis {$analysis->remotePipelineId}: "
                );
            }

            $files = $this->uploadService->uploadsForAnalysis(
                $analysis
            );

            if (empty($files) ) {
                if (!$output->isQuiet()) {
                    $output->writeln('No more files to upload.');
                }
                continue;
            }

            $uploads = $analysis->getUploads();

            $newFiles = $files->filter(
                fn($file) => !$uploads->exists(
                    fn($_, Upload $upload) => $upload->fileName === $file &&
                        $upload->isUploaded()
                )
            );

            if ($newFiles->count() === 0) {
                if (!$output->isQuiet()) {
                    $output->writeln('No more valid files to upload.');
                }
                continue;
            }

            if (!$output->isQuiet()) {
                $output->writeln('');
            }

            foreach ($newFiles as $file) {
                if (filesize($file) > pow(1024, 3)) {
                    $upload = $this->generateError(
                        $uploads,
                        $analysis,
                        $file,
                        UploadFileError::TooLarge,
                    );
                    $this->uploads->save($upload);
                    $output->writeln(
                        "Checking $file failed: " .
                        UploadFileError::TooLarge->name
                    );
                    continue;
                }

                $result = $this->api->uploadFile($analysis, $file);
                if ($result instanceof Upload) {
                    $existing = $this->findError(
                        $uploads,
                        $file
                    );

                    if ($existing) {
                        $this->uploads->remove($existing);
                    }

                    $this->uploads->save($result, true);
                    $output->writeln("File $file uploaded.");
                    continue 2;
                }

                if ($result === UploadFileError::NoSuchAnalysis) {
                    // ToDo Mark analysis as crashed
                }

                $error = $this->generateError(
                    $uploads,
                    $analysis,
                    $file,
                    $result,
                    uploaded: match($result) {
                        UploadFileError::AlreadyUploaded => true,
                        default => false,
                    }
                );
                $this->uploads->save($error, true);
                $output->writeln(
                    "File $file failed: {$result->name}"
                );
            }
        }
    }

    /**
     * @param Collection $uploads
     */
    private function generateError(
        Collection $uploads,
        Analysis $analysis,
        string $file,
        UploadFileError $error,
        bool $uploaded = false,
    ) {
        $existing = $this->findError($uploads, $file);
        if ($existing) {
            $existing->setError($error);
            if ($uploaded) {
                $existing->setUploaded();
            }

            return $existing;
        }

        return new Upload(
            $file,
            $analysis,
            error: $error,
            uploaded: $uploaded,
        );
    }

    private function findError(Collection $uploads, string $file): ?Upload
    {
        /** @var Upload $upload */
        foreach ($uploads as $upload) {
            if ($upload->fileName === $file) {
                return $upload;
            }
        }

        return null;
    }
}
