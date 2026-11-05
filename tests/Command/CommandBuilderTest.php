<?php

declare(strict_types=1);

namespace Unimarc2Marc21\Tests\Command;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Unimarc2Marc21\Command\CommandBuilder;
use Unimarc2Marc21\Command\CommandRunner;
use Unimarc2Marc21\Enums\QueryType;
use Unimarc2Marc21\Models\Z3950;

final class CommandBuilderTest extends TestCase
{
    public function testBuildCommandForIsbn(): void
    {
        $z3950 = new Z3950('z3950.nlg.gr', '210', 'biblios', '978-960-044-259-5', 'ISBN (Preferred)');

        $this->assertSame(
            [
                APP_BASH_MAIN,
                'z3950.nlg.gr',
                '210',
                'biblios',
                'Unimarc',
                '@attr 1=7 "978-960-044-259-5"',
                '978-960-044-259-5.mrc',
                STORAGE,
                TRANSFORM_TEMPLATE_UNIMARC_TO_MARC21,
            ],
            CommandBuilder::buildCommand($z3950)
        );
    }

    public function testBuildCommandForTitleUsesSafeFileName(): void
    {
        $command = CommandBuilder::buildCommand(new Z3950('host', '210', 'db', 'Η Οδύσσεια', 'Title'));

        $this->assertSame('@attr 1=4 "Η Οδύσσεια"', $command[5]);
        $this->assertSame('Η_Οδύσσεια.mrc', $command[6]);
    }

    public function testBuildCommandRejectsUnknownType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        CommandBuilder::buildCommand(new Z3950('host', '210', 'db', 'x', 'Author'));
    }

    public function testQueryTermCannotBreakOutOfQuotesOrInjectCommands(): void
    {
        $this->assertSame(
            '@attr 1=7 "123 show q"',
            CommandBuilder::buildQuery(QueryType::Isbn, "123\"\nshow\nq")
        );
    }

    public function testShellCharactersAreNeverInterpreted(): void
    {
        $marker = sys_get_temp_dir() . '/u2m-injection-' . uniqid();
        $value = '$(touch ' . $marker . ')`touch ' . $marker . '`';

        // Run the built argument vector through a harmless program instead of the real script
        $command = CommandBuilder::buildCommand(new Z3950('host', '210', 'db', $value, 'ISBN (Preferred)'));
        $command[0] = 'true';

        $this->assertSame(0, CommandRunner::run($command));
        $this->assertFileDoesNotExist($marker);
    }

    public function testBashScriptAndTemplateExist(): void
    {
        $this->assertFileExists(APP_BASH_MAIN);
        $this->assertTrue(is_executable(APP_BASH_MAIN), 'bash entrypoint must be executable');
        $this->assertFileExists(TRANSFORM_TEMPLATE_UNIMARC_TO_MARC21);
    }
}
