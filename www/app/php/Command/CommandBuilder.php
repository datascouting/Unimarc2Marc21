<?php

declare(strict_types=1);

namespace Unimarc2Marc21\Command;

use InvalidArgumentException;
use Unimarc2Marc21\Enums\QueryType;
use Unimarc2Marc21\Models\Z3950;
use Unimarc2Marc21\Utils\Utils;

final class CommandBuilder
{
    private const string SOURCE_FORMAT = "Unimarc";

    private function __construct()
    {
    }

    /**
     * PQF query for yaz-client, e.g. `@attr 1=7 "978-960-044-259-5"`.
     */
    public static function buildQuery(QueryType $type, string $value): string
    {
        return sprintf('@attr %s "%s"', $type->attribute(), self::sanitizeTerm($value));
    }

    /**
     * Removes characters that would break out of the quoted PQF term or inject
     * yaz-client commands (double quotes and control characters such as newlines).
     */
    public static function sanitizeTerm(string $value): string
    {
        $value = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $value) ?? '';

        return trim(str_replace('"', '', $value));
    }

    /**
     * Argument vector for the conversion script. Run it without a shell
     * (see CommandRunner), so no value is ever interpreted by the shell.
     *
     * @return list<string>
     * @throws InvalidArgumentException when the search type is not supported
     */
    public static function buildCommand(Z3950 $z3950): array
    {
        $type = $z3950->getQueryType()
            ?? throw new InvalidArgumentException(sprintf("Unsupported search type: %s", $z3950->getType()));

        return [
            APP_BASH_MAIN,
            $z3950->getHost(),
            $z3950->getPort(),
            $z3950->getDatabase(),
            self::SOURCE_FORMAT,
            self::buildQuery($type, $z3950->getValue()),
            Utils::toFileName($z3950->getValue()) . ".mrc",
            STORAGE,
            TRANSFORM_TEMPLATE_UNIMARC_TO_MARC21,
        ];
    }
}
