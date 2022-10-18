<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio\Queue;

class Queue implements Interfaces\Queue
{

    public function __construct(private readonly string $queueName) {
    }

    public function getDestinationIdentifier(): string
    {
        return $this->queueName;
    }
}