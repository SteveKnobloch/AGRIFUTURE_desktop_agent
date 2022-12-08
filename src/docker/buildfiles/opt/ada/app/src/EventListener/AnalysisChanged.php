<?php
declare(strict_types = 1);

namespace App\EventListener;

use App\Entity\Analysis;
use App\Entity\Upload;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Contracts\Cache\CacheInterface;

final class AnalysisChanged
{
    public function __construct(
        private readonly CacheInterface $remoteAnalysesCache
    ) {}

    public function __invoke(Analysis|Upload $analysis, LifecycleEventArgs $event)
    {
        $id = $analysis instanceof Upload ?
            $analysis->analysis->localUuid :
            $analysis->localUuid;
        $this->remoteAnalysesCache->delete($id->toBinary());
    }
}
