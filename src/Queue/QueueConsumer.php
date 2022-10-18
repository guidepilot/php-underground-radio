<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio\Queue;

use GuidePilot\UndergroundRadio\Interfaces\Message;
use GuidePilot\UndergroundRadio\Interfaces\Producer;
use GuidePilot\UndergroundRadio\Interfaces\RadioContext;
use GuidePilot\UndergroundRadio\Queue\Interfaces\ProcessorResult;
use GuidePilot\UndergroundRadio\Queue\Interfaces\Queue;
use GuidePilot\UndergroundRadio\Queue\Interfaces\QueueConsumerGroup;
use GuidePilot\UndergroundRadio\Queue\Interfaces\QueueMessageProcessor;
use GuidePilot\UndergroundRadio\UndergroundRadioException;

class QueueConsumer implements Interfaces\QueueConsumer
{

    const MESSAGE_HEADER_REDIS_MESSAGE_ID = 'redis.messageId';
    const MESSAGE_HEADER_REQUEUE_COUNT = 'queueConsumer.requeueCount';

    private int $maxRequeueCount = 0;

    public function __construct(
        private readonly RadioContext $context,
        private readonly string $consumerIdentifier,
        private readonly QueueConsumerGroup $queueConsumerGroup,
        private readonly ?Producer $requeueProducer = null
    ) {}

    public function consume(Queue $queue, QueueMessageProcessor $processor) {
        $this->context->queueConsumerGroupCreate(
            $queue->getDestinationIdentifier(),
            $this->queueConsumerGroup->getGroupIdentifier()
        );

        $checkBacklog = true;
        $lastId = '0-0';

        while (true) {
            $startFrom = $checkBacklog ? $lastId : '>';

            $rawMessages = $this->context->queueConsumerGroupConsume(
                $queue->getDestinationIdentifier(),
                $this->queueConsumerGroup->getGroupIdentifier(),
                $this->consumerIdentifier,
                $startFrom, 10, 10000
            );

            if (empty($rawMessages)) {
                $checkBacklog = false;
            }

            foreach ($rawMessages as $aQueueMessageId => $aQueueRawMessage) {


                $message = $this->context->getMessageSerializer()->toMessage($aQueueRawMessage);
                $message->addHeader(self::MESSAGE_HEADER_REDIS_MESSAGE_ID, $aQueueMessageId);

                $result = $processor->processMessage(
                    $message,
                    $queue
                );

                if ($result == ProcessorResult::Requeue) {
                    $this->handleRequeue($message, $queue, $processor);
                }

                $this->context->queueConsumerGroupAcknowledge(
                    $queue->getDestinationIdentifier(),
                    $this->queueConsumerGroup->getGroupIdentifier(),
                    $aQueueMessageId
                );

                $lastId = $aQueueMessageId;
            }
        }
    }

    public function getMaxRequeueCount(): int {
        return $this->maxRequeueCount;
    }

    public function setMaxRequeueCount(int $maxRequeueCount): void
    {
        $this->maxRequeueCount = $maxRequeueCount;
    }

    /**
     * @throws UndergroundRadioException
     */
    protected function handleRequeue(Message $message, Queue $queue, QueueMessageProcessor $processor): bool {

        if (!$this->requeueProducer) {
            throw new UndergroundRadioException('[QueueConsumer::handleRequeue] No requeue producer registered while trying to requeue');
        }

        $requeueCount = ($message->getHeader(self::MESSAGE_HEADER_REQUEUE_COUNT) ?? 0) + 1;

        if ($requeueCount >= $this->maxRequeueCount) {
            $processor->handleMaxRequeueCountReached($message, $queue);
            return false;
        }

        $message->addHeader(self::MESSAGE_HEADER_REQUEUE_COUNT, (string) $requeueCount);

        $this->requeueProducer->send($message, $queue);

        return true;

    }

    public function getConsumerIdentifier(): string {
        return $this->consumerIdentifier;
    }
}