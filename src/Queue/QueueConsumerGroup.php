<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio\Queue;

use GuidePilot\UndergroundRadio\Interfaces\RadioContext;

class QueueConsumerGroup implements Interfaces\QueueConsumerGroup
{

    public function __construct(private readonly string $groupIdentifier) {}

    public function getGroupIdentifier(): string
    {
        return $this->groupIdentifier;
    }
}