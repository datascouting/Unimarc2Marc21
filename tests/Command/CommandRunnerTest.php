<?php

declare(strict_types=1);

namespace Unimarc2Marc21\Tests\Command;

use PHPUnit\Framework\TestCase;
use Unimarc2Marc21\Command\CommandRunner;

final class CommandRunnerTest extends TestCase
{
    public function testReturnsZeroOnSuccess(): void
    {
        $this->assertSame(0, CommandRunner::run(['true']));
    }

    public function testReturnsAndLogsExitCodeOnFailure(): void
    {
        $this->expectErrorLog();

        $this->assertSame(3, CommandRunner::run(['sh', '-c', 'exit 3']));
    }

    public function testOutputIsDiscarded(): void
    {
        $this->expectOutputString('');
        CommandRunner::run(['sh', '-c', 'echo stdout; echo stderr >&2']);
    }

    public function testRunsInWorkingDirectory(): void
    {
        $file = sys_get_temp_dir() . '/u2m-cwd-' . uniqid();

        try {
            CommandRunner::run(['sh', '-c', 'pwd > "$0"', $file], sys_get_temp_dir());
            $this->assertSame(realpath(sys_get_temp_dir()), realpath(trim((string) file_get_contents($file))));
        } finally {
            @unlink($file);
        }
    }
}
