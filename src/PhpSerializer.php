<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio;

use GuidePilot\UndergroundRadio\Interfaces\Message;

class PhpSerializer implements Interfaces\MessageSerializer
{

    public function toString(Message $message): string {
        return serialize($message);
    }

    /**
     * @throws UndergroundRadioException
     */
    public function toMessage(string $serializedMessage): Message {
        $obj = unserialize($serializedMessage);

        if (!($obj instanceof Message)) {
            throw new UndergroundRadioException('PhpSerializer::toMessage unknown deserialized message object');
        }

        return $obj;
    }
}