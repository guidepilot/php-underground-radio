<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio;

use GuidePilot\UndergroundRadio\Interfaces\MessageSerializer;
use Redis;
use RedisException;

class RedisRadioContext implements Interfaces\RadioContext {

    const PACK_MESSAGE_KEY = 'm';

    private ?Redis $redis = null;

    public function __construct(private readonly RedisConfig $redisConfig, private readonly MessageSerializer $messageSerializer) {}

    /**
     * @throws RedisException
     * @throws UndergroundRadioException
     *
     */
    protected function connect(): void {
        if ($this->redis) {
            return;
        }

        $this->redis = new Redis();

        if ($this->redisConfig->usePersistentConnection) {
            if (!$this->redis->pconnect(
                $this->redisConfig->host,
                $this->redisConfig->port
            )) {
                throw new UndergroundRadioException("[Redis::connect] Connect (persistent) to {$this->redisConfig->host}:{$this->redisConfig->port} failed");
            }
        } else {
            if (!$this->redis->connect(
                $this->redisConfig->host,
                $this->redisConfig->port
            )) {
                throw new UndergroundRadioException("[Redis::connect] Connect to {$this->redisConfig->host}:{$this->redisConfig->port} failed");
            }
        }



        $this->redis->setOption(Redis::OPT_READ_TIMEOUT, -1);
    }

    /**
     * @throws RedisException
     */
    protected function disconnect(): void {
        $this->redis?->close();
    }

    public function getMessageSerializer(): MessageSerializer
    {
        return $this->messageSerializer;
    }


    /**
     * @throws UndergroundRadioException
     * @throws RedisException
     */
    public function getRedis(): Redis {
        if (!$this->redis) {
            $this->connect();
        }

        return $this->redis;
    }


    /**
     * @throws UndergroundRadioException
     * @throws RedisException
     */
    public function publish(string $channelName, string $message): void {
        $this->getRedis()->publish($channelName, $message);
    }

    /**
     * @throws UndergroundRadioException
     * @throws RedisException
     */
    public function subscribe(string $channelName, callable $callback): void {
        $this->redisSubscribe([$channelName], function (
            Redis $instance, string $channelName, string $message
        ) use ($callback) {
            $callback($this, $channelName, $message);
        });
    }

    /**
     * @throws UndergroundRadioException
     * @throws RedisException
     */
    protected function redisSubscribe(array $channelNames, callable $callback) {
        $this->getRedis()->subscribe($channelNames, $callback);
    }

    /**
     * @throws UndergroundRadioException
     * @throws RedisException
     */
    public function queueAdd(string $queueName, string $message, int $maxLength = 0, $isApproximateMaxLength = true): void {
        $this->getRedis()->xAdd($queueName, '*', [self::PACK_MESSAGE_KEY => $message], $maxLength, ($maxLength && $isApproximateMaxLength));
    }

    /**
     * @throws UndergroundRadioException
     * @throws RedisException
     */
    public function queueConsumerGroupCreate(string $queueName, string $groupName): void {
        $this->getRedis()->xGroup('CREATE', $queueName, $queueName, '$');
    }

    /**
     * @throws UndergroundRadioException
     * @throws RedisException
     */
    public function queueConsumerGroupConsume(string $queueName, string $groupName, string $consumerName, ?string $from = null, int $count = 1, int $block = 2000): array {
        if (!$from) {
            $from = '>';
        }

        $readData = $this->getRedis()->xReadGroup($groupName, $consumerName, [$queueName => $from], $count, $block);
        $queueRawMessages = $readData[$queueName] ?? [];

        $unpackedRawMessages = [];

        foreach ($queueRawMessages as $aQueueMessageId => $aQueueRawMessage) {
            if (!$unpackedRawMessage = $aQueueRawMessage[self::PACK_MESSAGE_KEY]) {
                continue;
            }

            $unpackedRawMessages[$aQueueMessageId] = $unpackedRawMessage;
        }

        return $unpackedRawMessages;
    }

    /**
     * @throws UndergroundRadioException
     * @throws RedisException
     */
    public function queueConsumerGroupAcknowledge(string $queueName, string $groupName, string $messageId): void {
        $this->getRedis()->xAck($queueName, $groupName, [$messageId]);
    }
}