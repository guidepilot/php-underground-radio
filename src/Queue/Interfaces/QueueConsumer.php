<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio\Queue\Interfaces;

use GuidePilot\UndergroundRadio\Interfaces\Consumer;

interface QueueConsumer extends Consumer
{
    public function consume(Queue $queue, QueueMessageProcessor $processor);
}