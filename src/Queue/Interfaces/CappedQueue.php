<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio\Queue\Interfaces;

interface CappedQueue extends Queue {

    public function getMaximumQueueLength(): int;

}