<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio\Broadcast;

use GuidePilot\UndergroundRadio\Broadcast\Interfaces\SubscriptionMessageProcessor;
use GuidePilot\UndergroundRadio\Interfaces\RadioContext;
use Redis;

class Subscriber implements Interfaces\Subscriber {

    private string $consumerIdentifier;

    public function __construct(private readonly RadioContext $context, ?string $consumerIdentifier = null) {
        $this->consumerIdentifier = $consumerIdentifier ?? uniqid('ur-subsscr');
    }

    public function subscribe(Channel $channel, SubscriptionMessageProcessor $processor) {
        $this->context->subscribe($channel->getDestinationIdentifier(), function (
            Redis $instance, string $channelName, string $message
        ) use ($processor, $channel) {

            $processor->processMessage(
                $this->context->getMessageSerializer()->toMessage($message),
                $channel
            );

        });
    }

    public function getConsumerIdentifier(): string {
        return $this->consumerIdentifier;
    }
}