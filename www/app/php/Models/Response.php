<?php

declare(strict_types=1);

namespace Unimarc2Marc21\Models;

final readonly class Response
{
    public function __construct(
        private int     $statusCode,
        private string  $message,
        private string  $value = '',
        private ?string $xml = null,
    ) {
    }

    public static function withValue(int $statusCode, string $message, string $value): self
    {
        return new self($statusCode, $message, $value);
    }

    public static function withValueAndXml(int $statusCode, string $message, string $value, string $xml): self
    {
        return new self($statusCode, $message, $value, $xml);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getXml(): ?string
    {
        return $this->xml;
    }
}
