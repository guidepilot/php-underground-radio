<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio\Interfaces;

interface RadioContext {

    public function publish(string $channelName, string $message): void;

    /**
     * @param string $channelName
     * @param callable $callback A callable with the signature f(RadioContext $context, string $channelName, string $message)
     * @return void
     */
    public function subscribe(string $channelName, callable $callback): void;

    public function queueAdd(string $queueName, string $message, int $maxLength = 0, $isApproximateMaxLength = true): void;
    public function queueConsumerGroupCreate(string $queueName, string $groupName): void;
    public function queueConsumerGroupConsume(string $queueName, string $groupName, string $consumerName, ?string $from = null, int $count = 1, int $block = 2000): array;
    public function queueConsumerGroupAcknowledge(string $queueName, string $groupName, string $messageId): void;

    public function getMessageSerializer(): MessageSerializer;

}