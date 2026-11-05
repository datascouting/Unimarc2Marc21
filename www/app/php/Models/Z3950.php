<?php

declare(strict_types=1);

namespace Unimarc2Marc21\Models;

use Unimarc2Marc21\Enums\QueryType;
use Unimarc2Marc21\Utils\Utils;

final readonly class Z3950
{
    public function __construct(
        private string $host,
        private string $port,
        private string $database,
        private string $value,
        private string $type,
    ) {
    }

    public static function fromRequest(array $request): self
    {
        return new self(
            Utils::stringValue($request, 'z3950_host'),
            Utils::stringValue($request, 'z3950_port'),
            Utils::stringValue($request, 'z3950_database'),
            Utils::stringValue($request, 'z3950_value'),
            Utils::stringValue($request, 'z3950_type'),
        );
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): string
    {
        return $this->port;
    }

    public function getDatabase(): string
    {
        return $this->database;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * The search type, or null when the posted type is not supported.
     */
    public function getQueryType(): ?QueryType
    {
        return QueryType::tryFrom($this->type);
    }
}
