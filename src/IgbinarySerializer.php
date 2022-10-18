<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio;

use GuidePilot\UndergroundRadio\Interfaces\Message;
use GuidePilot\UndergroundRadio\Interfaces\MessageSerializer;

class IgbinarySerializer implements MessageSerializer
{

    /**
     * @throws UndergroundRadioException
     */
    public function toString(Message $message): string
    {
        if (!function_exists('igbinary_serialize')) {
           throw new UndergroundRadioException('IgbinarySerializer::toString igbinary_serialize function not defined');
        }

        return igbinary_serialize($message);
    }

    /**
     * @throws UndergroundRadioException
     */
    public function toMessage(string $serializedMessage): Message
    {
        if (!function_exists('igbinary_unserialize')) {
            throw new UndergroundRadioException('IgbinarySerializer::toString igbinary_serialize function not defined');
        }

        $obj = igbinary_unserialize($serializedMessage);

        if (!($obj instanceof Message)) {
            throw new UndergroundRadioException('PhpSerializer::toMessage unknown deserialized message object');
        }

        return $obj;
    }
}