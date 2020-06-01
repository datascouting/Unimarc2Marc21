<?php

require_once APP_PHP_PATH . "/enums/QueryType.php";

abstract class CommandBuilder
{

    /**
     * @param string $attribute
     * @param string $value
     * @return string
     */
    private static function buildQuery(string $attribute, string $value): string
    {
        return sprintf("@attr %s \\\"%s\\\"", QueryType::toQuery($attribute), $value);
    }

    /**
     * @param string $value
     * @return string
     */
    private static function buildFileName(string $value): string
    {
        return sprintf("%s.mrc", $value);
    }

    /**
     * @return string
     */
    private static function buildStartCommand(): string
    {
        return sprintf("cd %s && %s", APP_BASH_PATH, APP_BASH_MAIN);
    }

    /**
     * @param Z3950 $z3950
     * @return string
     */
    public static function buildCommand(Z3950 $z3950)
    {
        $sourceType = "Unimarc";
        $type = $z3950->getType();
        $value = $z3950->getValue();

        return sprintf(
            "%s \"%s\" \"%s\" \"%s\" \"%s\" \"%s\" \"%s\" \"%s\" \"%s\"",
            CommandBuilder::buildStartCommand(),
            $z3950->getHost(),
            $z3950->getPort(),
            $z3950->getDatabase(),
            $sourceType,
            CommandBuilder::buildQuery($type, $value),
            CommandBuilder::buildFileName($value),
            STORAGE,
            TRANSFORM_TEMPLATE_UNIMARC_TO_MARC21
        );
    }
}