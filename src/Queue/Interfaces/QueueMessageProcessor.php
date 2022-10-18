<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio\Queue\Interfaces;

use GuidePilot\UndergroundRadio\Interfaces\Message;

interface QueueMessageProcessor {

    public function processMessage(Message $message, Queue $queue): ProcessorResult;

    public function handleMaxRequeueCountReached(Message $message, Queue $queue);

}