<?php
declare(strict_types = 1);

namespace App\Service;

use App\Enum\FileFormat;

final class UploadService
{
    private const directory = '/data';
    private const suffixes = [
        // If upgrading to ^8.2, this can be replaced with the enum values.
        'chemical/seq-na-fastq' => [
            'fastq',
            'fq',
        ],
        'chemical/x-seq-na-fast5' => [
            'fast5',

        ],
    ];

    public function getValidDirectories(): \Generator
    {
        $trim = strlen(self::directory);

        $traverse = function (string $directory) use (&$traverse, $trim): \Generator {
            yield substr($directory, $trim) ?: '/';

            $contents = array_diff(
                scandir($directory),
                ['.', '..']
            );

            foreach ($contents as $content) {
                if (is_dir("$directory/$content")) {
                    foreach ($traverse("$directory/$content") as $item) {
                        yield $item;
                    }
                }
            }
        };

        return $traverse(self::directory);
    }

    public function containsSequence(
        string $directory,
        ?FileFormat $format = null,
    ): bool {
        $directory = self::directory . "/$directory";
        $suffixes = $format ?
            self::suffixes[$format->value] :
            call_user_func_array(
                'array_merge',
                array_values(self::suffixes),
            );

        $contents = scandir($directory);
        foreach ($contents as $content) {
            foreach ($suffixes as $suffix) {
                if(str_ends_with($content, ".$suffix")) {
                    return true;
                }
            }
        }

        return false;
    }
}
