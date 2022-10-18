<?php
declare(strict_types=1);

require '../vendor/autoload.php';

use GuidePilot\UndergroundRadio\JsonSerializer;
use GuidePilot\UndergroundRadio\Message;
use GuidePilot\UndergroundRadio\PhpSerializer;
use GuidePilot\UndergroundRadio\Producer;
use GuidePilot\UndergroundRadio\Queue\CappedQueue;
use GuidePilot\UndergroundRadio\Queue\Queue;
use GuidePilot\UndergroundRadio\RedisConfig;
use GuidePilot\UndergroundRadio\RedisRadioContext;

$redisConfig = new RedisConfig('localhost');
//$serializer = new JsonSerializer();
$serializer = new PhpSerializer();
$context = new RedisRadioContext($redisConfig,$serializer);
$producer = new Producer($context);

$queue = new CappedQueue('fooQueue', 42);

$message = new Message(uniqid('ugr'));
$message->addHeader('cli-test', "1");
$message->setBody('Hello queue world! (capped)');

$producer->send($message, $queue);

