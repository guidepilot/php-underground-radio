<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio\Interfaces;

interface Consumer
{
    public function getConsumerIdentifier(): string;
}