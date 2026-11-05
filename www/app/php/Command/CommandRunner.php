<?php

declare(strict_types=1);

namespace Unimarc2Marc21\Command;

final class CommandRunner
{
    private function __construct()
    {
    }

    /**
     * Runs a command directly (no shell), discarding its output.
     * Failures are logged together with the command's stderr.
     *
     * @param list<string> $command program followed by its arguments
     * @return int the exit code (-1 when the process could not be started)
     */
    public static function run(array $command, ?string $workingDirectory = null): int
    {
        $descriptors = [
            0 => ['file', '/dev/null', 'r'],
            1 => ['file', '/dev/null', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptors, $pipes, $workingDirectory);

        if ($process === false) {
            implode(' ', $command)
                |> (fn($x) => sprintf("Can't start command: %s", $x))
                |> error_log(...);

            return -1;
        }

        $stderr = (string) stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            error_log(sprintf(
                "Command failed with exit code %d: %s %s",
                $exitCode,
                implode(' ', $command),
                trim($stderr)
            ));
        }

        return $exitCode;
    }
}
