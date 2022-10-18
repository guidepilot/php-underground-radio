<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio\Interfaces;

interface RadioContext {

    public function publish(string $channelName, string $message);
    public function subscribe(string $channelName, callable $callback);

    public function queueAdd(string $queueName, string $message, int $maxLength = 0, $isApproximateMaxLength = true);
    public function queueConsumerGroupCreate(string $queueName, string $groupName);
    public function queueConsumerGroupConsume(string $queueName, string $groupName, string $consumerName, ?string $from = null, int $count = 1, int $block = 2000);
    public function queueConsumerGroupAcknowledge(string $queueName, string $groupName, string $messageId);

    public function getMessageSerializer(): MessageSerializer;

}