<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio\Interfaces;

interface MessageSerializer
{
    public function toString(Message $message): string;
    public function toMessage(string $serializedMessage): Message;
}