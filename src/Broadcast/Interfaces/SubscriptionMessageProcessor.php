<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio\Broadcast\Interfaces;

use GuidePilot\UndergroundRadio\Interfaces\Message;

interface SubscriptionMessageProcessor {

    public function processMessage(Message $message, Channel $channel);

}