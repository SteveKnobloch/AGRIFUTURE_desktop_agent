<?php
declare(strict_types = 1);

namespace App\Enum;

enum AnalysisStatus: string
{
    case running = 'running';
    case paused = 'paused';
    case completed = 'completed';
    case crashed = 'crashed';
    case unknown = 'unknown';

    public function shouldUploadFiles(): bool
    {
        return match ($this) {
            self::running => true,
            default => false,
        };
    }

    public function isFinished(): bool
    {
        return match ($this) {
            self::completed, self::crashed => true,
            default => false,
        };
    }
}
