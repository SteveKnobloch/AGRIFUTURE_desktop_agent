<?php
declare(strict_types = 1);

namespace App\Entity;

final class RemoteUpload
{
    /** @internal */
    public function __construct(
        public readonly string $fileName,
        public readonly ?int $size,
        public readonly ?int $crc32,
    ) {}
}
