<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio\Broadcast\Interfaces;

use GuidePilot\UndergroundRadio\Broadcast\Channel;
use GuidePilot\UndergroundRadio\Interfaces\Consumer;

interface Subscriber extends Consumer
{
    public function subscribe(Channel $channel, SubscriptionMessageProcessor $processor);
}