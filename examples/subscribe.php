<?php
declare(strict_types=1);

require '../vendor/autoload.php';

use GuidePilot\UndergroundRadio\Broadcast\Channel;
use GuidePilot\UndergroundRadio\Broadcast\Interfaces\SubscriptionMessageProcessor;
use GuidePilot\UndergroundRadio\Broadcast\Subscriber;
use GuidePilot\UndergroundRadio\JsonSerializer;
use GuidePilot\UndergroundRadio\RedisConfig;
use GuidePilot\UndergroundRadio\RedisRadioContext;


$redisConfig = new RedisConfig('localhost');

$serializer = new JsonSerializer();
$context = new RedisRadioContext($redisConfig,$serializer);
$subscriber = new Subscriber($context);

$channel = new Channel('fooChannel');

$subscriber->subscribe($channel, new class implements SubscriptionMessageProcessor {

    public function processMessage(\GuidePilot\UndergroundRadio\Interfaces\Message $message, \GuidePilot\UndergroundRadio\Broadcast\Interfaces\Channel $channel) {
        echo "--- New message from {$channel->getDestinationIdentifier()} ---".PHP_EOL;
        print_r($message);
        echo PHP_EOL;
    }
});


