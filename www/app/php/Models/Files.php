<?php

declare(strict_types=1);

namespace Unimarc2Marc21\Models;

use Unimarc2Marc21\Utils\Utils;

/**
 * The MARC21 output files produced for a search value.
 */
final readonly class Files
{
    public function __construct(
        private string $value,
        private string $xml,
        private string $mrc,
    ) {
    }

    public static function fromValue(string $value): self
    {
        $base = sprintf("%s/%s.mrc.marc21", STORAGE_OUTPUT, Utils::toFileName($value));

        return new self($value, $base . ".xml", $base . ".mrc");
    }

    public static function none(): self
    {
        return new self('', '', '');
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getXml(): string
    {
        return $this->xml;
    }

    public function getMrc(): string
    {
        return $this->mrc;
    }

    public function existsBoth(): bool
    {
        return is_file($this->xml) && is_file($this->mrc);
    }

    public function getXmlContents(): string
    {
        if (!$this->existsBoth()) {
            return "";
        }

        return (string) file_get_contents($this->xml);
    }
}
