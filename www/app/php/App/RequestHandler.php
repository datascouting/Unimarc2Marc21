<?php

declare(strict_types=1);

namespace Unimarc2Marc21\App;

use RuntimeException;
use Unimarc2Marc21\Command\CommandBuilder;
use Unimarc2Marc21\Command\CommandRunner;
use Unimarc2Marc21\Koha\KohaBiblioPost;
use Unimarc2Marc21\Models\Files;
use Unimarc2Marc21\Models\Koha;
use Unimarc2Marc21\Models\Response;
use Unimarc2Marc21\Models\Z3950;
use Unimarc2Marc21\Utils\Utils;

/**
 * Turns a posted form into a Response: Z39.50 search + conversion, or import to Koha.
 */
final class RequestHandler
{
    private function __construct()
    {
    }

    public static function handle(array $request): Response
    {
        if (Utils::isSearchAndConvertRequest($request)) {
            return self::searchAndConvert($request);
        }

        if (Utils::isImportToKohaRequest($request)) {
            return self::importToKoha($request);
        }

        return new Response(STATUS_WAIT, "Waiting for request...");
    }

    public static function searchAndConvert(array $request): Response
    {
        $z3950 = Z3950::fromRequest($request);
        $files = Files::fromValue($z3950->getValue());

        if ($files->existsBoth()) {
            return Response::withValue(STATUS_SEARCH_OK, "Found, from cache...", $z3950->getValue());
        }

        if ($z3950->getQueryType() === null) {
            return new Response(STATUS_SEARCH_NOT_OK, "Unsupported type of value to find...");
        }

        CommandRunner::run(CommandBuilder::buildCommand($z3950), APP_BASH_PATH);

        if ($files->existsBoth()) {
            return Response::withValue(STATUS_SEARCH_OK, "Found, from Z39.50...", $z3950->getValue());
        }

        return new Response(STATUS_SEARCH_NOT_OK, "Error... We can't find it...");
    }

    public static function importToKoha(array $request): Response
    {
        $koha = Koha::fromRequest($request);

        try {
            $kohaResponse = KohaBiblioPost::post($koha, Files::fromValue($koha->getValue())->getXml());

            return Response::withValueAndXml(STATUS_IMPORT_OK, "Imported to Koha...", $koha->getValue(), $kohaResponse);
        } catch (RuntimeException $e) {
            error_log($e->getMessage());

            return Response::withValue(
                STATUS_IMPORT_NOT_OK,
                "Can't import to Koha... Please check the Intra settings and try again...",
                $koha->getValue()
            );
        }
    }
}
