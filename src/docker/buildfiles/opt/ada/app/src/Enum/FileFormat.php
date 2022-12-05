<?php
declare(strict_types = 1);

namespace App\Enum;

enum FileFormat: string
{
    case fastq = 'chemical/seq-na-fastq';
    case fast5 = 'chemical/x-seq-na-fast5';
}
