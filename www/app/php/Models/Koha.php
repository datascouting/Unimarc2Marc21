<?php

declare(strict_types=1);

namespace Unimarc2Marc21\Models;

use Unimarc2Marc21\Utils\Utils;

final readonly class Koha
{
    public function __construct(
        private string $host,
        private string $port,
        private string $basicAuthUsername,
        private string $basicAuthPassword,
        private string $username,
        private string $password,
        private string $value,
    ) {
    }

    public static function fromRequest(array $request): self
    {
        return new self(
            Utils::stringValue($request, 'koha_intra_host'),
            Utils::stringValue($request, 'koha_intra_port'),
            Utils::stringValue($request, 'koha_intra_basic_username'),
            Utils::stringValue($request, 'koha_intra_basic_password'),
            Utils::stringValue($request, 'koha_intra_username'),
            Utils::stringValue($request, 'koha_intra_password'),
            Utils::stringValue($request, 'koha_intra_value'),
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

    public function getBasicAuthUsername(): string
    {
        return $this->basicAuthUsername;
    }

    public function getBasicAuthPassword(): string
    {
        return $this->basicAuthPassword;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
