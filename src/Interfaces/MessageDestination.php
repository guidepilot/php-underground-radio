<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio\Interfaces;

interface MessageDestination {

    public function getDestinationIdentifier(): string;

}