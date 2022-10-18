<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio;

use GuidePilot\UndergroundRadio\Broadcast\Interfaces\Channel;
use GuidePilot\UndergroundRadio\Interfaces\Message;
use GuidePilot\UndergroundRadio\Interfaces\MessageDestination;
use GuidePilot\UndergroundRadio\Interfaces\RadioContext;
use GuidePilot\UndergroundRadio\Queue\CappedQueue;
use GuidePilot\UndergroundRadio\Queue\Interfaces\Queue;

class Producer implements Interfaces\Producer
{

    public function __construct(private readonly RadioContext $context) {
    }

    /**
     * @throws UndergroundRadioException
     */
    public function send(Message $message, MessageDestination $destination) {

        if ($destination instanceof Channel) {
            $this->context->publish(
                $destination->getDestinationIdentifier(),
                $this->context->getMessageSerializer()->toString($message)
            );
        } else if ($destination instanceof Queue) {
            $this->context->queueAdd(
                $destination->getDestinationIdentifier(),
                $this->context->getMessageSerializer()->toString($message),
                ($destination instanceof CappedQueue) ? $destination->getMaximumQueueLength() : 0
            );
        } else {
            throw new UndergroundRadioException('[Producer::send] No known handler for type of MessageDestination');
        }

    }
}