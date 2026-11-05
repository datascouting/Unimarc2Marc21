<?php

declare(strict_types=1);

/**
 * Default Z39.50 server settings, used when the request has no Z39.50 settings.
 *
 * @return array<string, string>
 */
function getDefaultZ3950(): array
{
    return [
        "z3950_host" => "z3950.nlg.gr",
        "z3950_port" => "210",
        "z3950_database" => "biblios",
        "z3950_type" => "ISBN (Preferred)",
    ];
}

/**
 * Default Koha settings, used when the request has no Koha settings.
 * Credentials can be supplied through the `username` / `password` query parameters.
 *
 * @return array<string, string>
 */
function getDefaultKoha(): array
{
    return [
        "koha_intra_host" => "",
        "koha_intra_port" => "80",
        "koha_intra_basic_username" => "",
        "koha_intra_basic_password" => "",
        "koha_intra_username" => is_string($_GET['username'] ?? null) ? $_GET['username'] : '',
        "koha_intra_password" => is_string($_GET['password'] ?? null) ? $_GET['password'] : '',
    ];
}
