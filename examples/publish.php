<?php
declare(strict_types=1);

require '../vendor/autoload.php';

use GuidePilot\UndergroundRadio\Broadcast\Channel;
use GuidePilot\UndergroundRadio\JsonSerializer;
use GuidePilot\UndergroundRadio\Message;
use GuidePilot\UndergroundRadio\Producer;
use GuidePilot\UndergroundRadio\RedisConfig;
use GuidePilot\UndergroundRadio\RedisRadioContext;

$redisConfig = new RedisConfig('localhost');
$serializer = new JsonSerializer();
$context = new RedisRadioContext($redisConfig, $serializer);

$producer = new Producer($context);

$channel = new Channel('fooChannel');

$message = new Message(uniqid('ugr'));
$message->addHeader('cli-test', "1");
$message->setBody('Hello world!');

$producer->send($message, $channel);

