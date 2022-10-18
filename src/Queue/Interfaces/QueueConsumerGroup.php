<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio\Queue\Interfaces;

interface QueueConsumerGroup
{
    public function getGroupIdentifier(): string;
}