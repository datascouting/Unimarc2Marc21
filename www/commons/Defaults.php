<?php

/**
 * @return array
 */
function getDefaultZ3950()
{
    return [
        "z3950_host" => "z3950.nlg.gr",
        "z3950_port" => "210",
        "z3950_database" => "biblios",
        'z3950_type' => "ISBN (Preferred)"
    ];
}

/**
 * @return array
 */
function getDefaultKoha()
{
    return [
        "koha_intra_host" => "",
        "koha_intra_port" => "80",
        "koha_intra_basic_username" => "",
        "koha_intra_basic_password" => "",
        "koha_intra_username" => $_GET['username'],
        "koha_intra_password" => $_GET['password']
    ];
}