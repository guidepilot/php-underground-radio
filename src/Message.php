<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio;

class Message implements Interfaces\Message
{

    private ?string $body = null;

    /**
     * @var array<string, string>
     */
    private array $headers = [];

    public function __construct(private readonly string $messageId) {}

    public function setBody(?string $body): void
    {
        $this->body = $body;
    }


    public function getBody(): ?string
    {
        return $this->body;
    }

    public function getMessageId(): string
    {
        return $this->messageId;
    }

    public function addHeader(string $headerName, string $headerValue) {
        $this->headers[$headerName] = $headerValue;
    }

    public function getHeader(string $headerName): ?string {
        return $this->headers[$headerName] ?? null;
    }

    public function getHeaders(): array {
        return $this->headers;
    }

}