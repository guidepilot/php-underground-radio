<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio\Queue;

class CappedQueue extends Queue implements Interfaces\CappedQueue
{

    public function __construct(string $queueName, private readonly int $maximumQueueLength) {
        parent::__construct($queueName);
    }

    public function getMaximumQueueLength(): int {
        return $this->maximumQueueLength;
    }
}