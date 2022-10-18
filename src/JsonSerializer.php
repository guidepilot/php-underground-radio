<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio;

use GuidePilot\UndergroundRadio\Interfaces\Message;

class JsonSerializer implements Interfaces\MessageSerializer {

    const SERIALIZE_KEY_MESSAGE_ID = 'id';
    const SERIALIZE_KEY_BODY = 'bd';
    const SERIALIZE_KEY_HEADERS = 'hd';

    public function toString(Message $message): string {
        return json_encode([
            self::SERIALIZE_KEY_MESSAGE_ID => $message->getMessageId(),
            self::SERIALIZE_KEY_HEADERS => $message->getHeaders(),
            self::SERIALIZE_KEY_BODY => $message->getBody(),
        ]);
    }

    /**
     * @throws UndergroundRadioException
     */
    public function toMessage(string $serializedMessage): Message {
        $messageData = json_decode($serializedMessage, true);

        if (!$messageId = $messageData[self::SERIALIZE_KEY_MESSAGE_ID] ?? null) {
            throw new UndergroundRadioException('[JsonSerializer::toMessage] Could not deserialize message. Message id missing.');
        }

        $message = new \GuidePilot\UndergroundRadio\Message($messageId);

        $message->setBody($messageData[self::SERIALIZE_KEY_BODY] ?? null);

        $headers = $messageData[self::SERIALIZE_KEY_HEADERS] ?? [];

        if (!is_array($headers)) {
            throw new UndergroundRadioException('[JsonSerializer::toMessage] Could not deserialize message. Headers invalid.');
        }

        foreach ($headers as $headerName => $headerValue) {
            $message->addHeader($headerName, $headerValue);
        }

        return $message;
    }
}