<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio;

class RedisConfig {

    public function __construct(
        public readonly string $host,
        public readonly int $port = 6379,
        public readonly bool $usePersistentConnection = false
    ) {}

}