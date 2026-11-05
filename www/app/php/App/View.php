<?php

declare(strict_types=1);

namespace Unimarc2Marc21\App;

use Unimarc2Marc21\Models\Response;
use Unimarc2Marc21\Utils\Utils;

/**
 * Template helpers for index.php.
 */
final class View
{
    private function __construct()
    {
    }

    /**
     * The result panels are hidden while waiting and when the search failed.
     */
    public static function mustHideObjects(Response $response): bool
    {
        return in_array($response->getStatusCode(), [STATUS_WAIT, STATUS_SEARCH_NOT_OK], true);
    }

    public static function getHideClassOrEmpty(Response $response): string
    {
        return self::mustHideObjects($response) ? "d-none" : "";
    }

    /**
     * HTML-escapes a value for output.
     */
    public static function e(string $value): string
    {
        return Utils::escape($value);
    }
}
