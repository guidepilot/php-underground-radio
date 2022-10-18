<?php
declare(strict_types=1);

require '../vendor/autoload.php';


use GuidePilot\UndergroundRadio\Interfaces\Message;
use GuidePilot\UndergroundRadio\PhpSerializer;
use GuidePilot\UndergroundRadio\Producer;
use GuidePilot\UndergroundRadio\Queue\Interfaces\ProcessorResult;
use GuidePilot\UndergroundRadio\Queue\Interfaces\QueueMessageProcessor;
use GuidePilot\UndergroundRadio\Queue\Queue;
use GuidePilot\UndergroundRadio\Queue\QueueConsumer;
use GuidePilot\UndergroundRadio\Queue\QueueConsumerGroup;
use GuidePilot\UndergroundRadio\RedisConfig;
use GuidePilot\UndergroundRadio\RedisRadioContext;

$redisConfig = new RedisConfig('localhost');
$serializer = new PhpSerializer();
$context = new RedisRadioContext($redisConfig, $serializer);
$group = new QueueConsumerGroup('worker');
$requeueProducer = new Producer($context);

$consumer = new QueueConsumer($context, 'worker-0', $group, $requeueProducer);
$consumer->setMaxRequeueCount(3);

$queue = new Queue('fooQueue');

$consumer->consume($queue, new class implements QueueMessageProcessor {

    public function processMessage(Message $message, \GuidePilot\UndergroundRadio\Queue\Interfaces\Queue $queue): ProcessorResult {
        echo "--- New message from {$queue->getDestinationIdentifier()} ---".PHP_EOL;
        print_r($message);
        echo PHP_EOL;

        return ProcessorResult::Acknowledge;
    }

    public function handleMaxRequeueCountReached(Message $message, \GuidePilot\UndergroundRadio\Queue\Interfaces\Queue $queue) {
        echo "!!! Message {$message->getMessageId()} reached max requeue count !!!".PHP_EOL;
    }

});





