<?php

require_once APP_PHP_PATH . "/utils/Utils.php";

require_once APP_PHP_PATH . "/command/CommandBuilder.php";
require_once APP_PHP_PATH . "/command/CommandRunner.php";
require_once APP_PHP_PATH . "/koha/KohaBiblioPost.php";

require_once APP_PHP_PATH . "/models/Z3950.php";
require_once APP_PHP_PATH . "/models/Koha.php";
require_once APP_PHP_PATH . "/models/Files.php";
require_once APP_PHP_PATH . "/models/Response.php";

/**
 * @param $request
 * @return Response
 */
function getSearchAndConvertRequestResponse($request): Response
{
    $z3950 = Z3950::fromRequest($request);
    $files = Files::fromvalue($z3950->getValue());

    if ($files->existsBoth()) {
        return Response::withvalue(
            STATUS_SEARCH_OK,
            "Found, from cache...",
            $z3950->getValue()
        );
    }

    $commandToRun = CommandBuilder::buildCommand($z3950);

    $message = CommandRunner::runCommand($commandToRun);
    if ($message != STATUS_SEARCH_OK) {
        error_log($message);
    }

    if ($files->existsBoth()) {
        return Response::withvalue(
            STATUS_SEARCH_OK,
            "Found, from Z39.50...",
            $z3950->getValue()
        );
    }

    return new Response(
        STATUS_SEARCH_NOT_OK,
        "Error... We can't find it..."
    );
}

/**
 * @param $request
 * @return Response
 */
function getImportToKohaRequestResponse($request): Response
{
    $koha = Koha::fromRequest($request);

    try {
        $kohaResponse = KohaBiblioPost::post(
            $koha->getBasicAuthUsername(),
            $koha->getBasicAuthPassword(),
            $koha->getHost(),
            $koha->getPort(),
            $koha->getUsername(),
            $koha->getPassword(),
            Utils::getFullFilePath(
                STORAGE_OUTPUT,
                sprintf("%s.mrc.marc21.xml", $koha->getValue())
            )
        );

        return Response::withvalueAndXML(
            STATUS_IMPORT_OK,
            "Imported to Koha...",
            $koha->getValue(),
            $kohaResponse
        );
    } catch (Exception $e) {
        return Response::withvalue(
            STATUS_IMPORT_NOT_OK,
            "Can't import to Koha... Please check the Intra settings and try again...",
            $koha->getValue()
        );
    }
}

/**
 * @param $request
 * @return Response
 */
function getResponse($request)
{
    if (Utils::isSearchAndConvertRequest($request)) {
        return getSearchAndConvertRequestResponse($request);
    }

    if (Utils::isImportToKohaRequest($request)) {
        return getImportToKohaRequestResponse($request);
    }

    return new Response(
        STATUS_WAIT,
        "Waiting for request..."
    );
}

/**
 * @param Response $response
 * @return bool
 */
function mustHideObjects(Response $response): bool
{
    if ($response->getStatusCode() === STATUS_WAIT) {
        return true;
    }

    if ($response->getStatusCode() === STATUS_SEARCH_NOT_OK) {
        return true;
    }

    if ($response->getStatusCode() === STATUS_IMPORT_NOT_OK) {
        return false;
    }

    return false;
}

/**
 * @param Response $response
 * @return string
 */
function getHideClassOrEmpty(Response $response): string
{
    return (mustHideObjects($response)) ? "d-none" : "";
}