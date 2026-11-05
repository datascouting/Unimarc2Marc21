<?php

use PHPUnit\Framework\TestCase;

final class CommandRunnerTest extends TestCase
{
    public function testSuccessfulCommandReturnsSearchOk(): void
    {
        $this->assertSame((string) STATUS_SEARCH_OK, CommandRunner::runCommand('true'));
    }

    public function testFailingCommandReturnsErrorWithExitCode(): void
    {
        $this->assertSame(
            'Error!!! Exit code = 3, for command exit 3',
            CommandRunner::runCommand('exit 3')
        );
    }

    public function testCommandOutputIsSuppressedAndBufferIsClosed(): void
    {
        $level = ob_get_level();

        $this->expectOutputString('');
        CommandRunner::runCommand('echo should-not-leak');

        $this->assertSame($level, ob_get_level());
    }
}
