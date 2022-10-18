<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio\Interfaces;

interface Message {
    public function setBody(?string $body): void;

    public function getBody(): ?string;

    public function getMessageId(): string;

    public function addHeader(string $headerName, string $headerValue);

    public function getHeader(string $headerName): ?string;

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array;
}