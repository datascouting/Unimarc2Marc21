<?php

abstract class CommandRunner
{
    /**
     * @param string $commandToRun
     * @return string
     */
    public static function runCommand(string $commandToRun): string
    {
        ob_start();
        system($commandToRun, $statusCode);
        ob_clean();

        if ($statusCode === 0) {
            return STATUS_SEARCH_OK;
        }

        return "Error!!! Exit code = $statusCode, for command $commandToRun";
    }
}