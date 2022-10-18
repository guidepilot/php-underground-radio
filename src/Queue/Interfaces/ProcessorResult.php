<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio\Queue\Interfaces;

enum ProcessorResult {
    case Acknowledge;
    case Reject;
    case Requeue;
}
