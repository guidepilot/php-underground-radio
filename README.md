# PHP UndergroundRadio

**Disclaimer**: This project is in a very early stage of development and should not be used in production. The present code is not much more than a first draft. Nevertheless, it may already be of use to someone. Pull requests always welcome...

## Introduction

The **UndergroundRadio** library allows messages to be sent via various services that implement both a message queue architecture and a publish/subscribe service.

Although the architecture provides an abstract interface for the integration of any suitable services, at this time only an implementation based on Redis exists. Here the focus is on the use of modern [Redis](https://redis.io/) features like [Streams](https://redis.io/docs/data-types/streams/) and [Pub/Sub](https://redis.io/docs/manual/pubsub/).

This library is inspired by [Enqueue](https://github.com/php-enqueue/enqueue-dev) library and the [queue-interop](https://github.com/queue-interop/queue-interop) protocol, but takes some different approaches in detail.

## Installation

This package requires **PHP 8.1** or greater!

Installing with composer:

```
$ composer require guidepilot/php-underground-radio
```

## Usage examples for pub/sub pattern

This pattern is also known as broadcast or fan-out architecture.

### Simple message producer via channel

```php
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

$message = new Message(uniqid());
$message->addHeader('cli-test', "1");
$message->setBody('Hello world!');

$producer->send($message, $channel);
```

### Simple message subscriber

```php
use GuidePilot\UndergroundRadio\Broadcast\Channel;
use GuidePilot\UndergroundRadio\Broadcast\Interfaces\SubscriptionMessageProcessor;
use GuidePilot\UndergroundRadio\Broadcast\Subscriber;
use GuidePilot\UndergroundRadio\JsonSerializer;
use GuidePilot\UndergroundRadio\RedisConfig;
use GuidePilot\UndergroundRadio\RedisRadioContext;


$redisConfig = new RedisConfig('localhost');
$serializer = new JsonSerializer();
$context = new RedisRadioContext($redisConfig, $serializer);

$subscriber = new Subscriber($context);
$channel = new Channel('fooChannel');

$subscriber->subscribe($channel, new class implements SubscriptionMessageProcessor {

    public function processMessage(\GuidePilot\UndergroundRadio\Interfaces\Message $message, \GuidePilot\UndergroundRadio\Broadcast\Interfaces\Channel $channel) {
        echo "--- New message from {$channel->getDestinationIdentifier()} ---".PHP_EOL;
        print_r($message);
        echo PHP_EOL;
    }
});
```

## Usage examples message queue pattern

### Simple message producer via queue

```php
use GuidePilot\UndergroundRadio\JsonSerializer;
use GuidePilot\UndergroundRadio\Message;
use GuidePilot\UndergroundRadio\Producer;
use GuidePilot\UndergroundRadio\Queue\CappedQueue;
use GuidePilot\UndergroundRadio\RedisConfig;
use GuidePilot\UndergroundRadio\RedisRadioContext;

$redisConfig = new RedisConfig('localhost');
$serializer = new JsonSerializer();
$context = new RedisRadioContext($redisConfig, $serializer);

$producer = new Producer($context);
$queue = new CappedQueue('fooQueue', 42);

$message = new Message(uniqid());
$message->addHeader('cli-test', "1");
$message->setBody('Hello queue world! (capped)');

$producer->send($message, $queue);
```

### Simple queue consumer

```php
use GuidePilot\UndergroundRadio\Interfaces\Message;
use GuidePilot\UndergroundRadio\PhpSerializer;
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
$consumer = new QueueConsumer($context, 'worker-0', $group);
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
```

## License

It is released under the [MIT License](LICENSE).

