<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio\Broadcast;

class Channel implements Interfaces\Channel
{

    public function __construct(private readonly string $channelName) {
    }

    public function getDestinationIdentifier(): string
    {
        return $this->channelName;
    }
}