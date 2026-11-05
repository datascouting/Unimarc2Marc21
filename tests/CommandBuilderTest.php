<?php

use PHPUnit\Framework\TestCase;

final class CommandBuilderTest extends TestCase
{
    public function testBuildCommandForIsbn(): void
    {
        $z3950 = new Z3950('z3950.nlg.gr', '210', 'biblios', '9789600000000', 'ISBN (Preferred)');

        $expected = sprintf(
            'cd %s && %s "z3950.nlg.gr" "210" "biblios" "Unimarc" "@attr 1=7 \"9789600000000\"" "9789600000000.mrc" "%s" "%s"',
            APP_BASH_PATH,
            APP_BASH_MAIN,
            STORAGE,
            TRANSFORM_TEMPLATE_UNIMARC_TO_MARC21
        );

        $this->assertSame($expected, CommandBuilder::buildCommand($z3950));
    }

    public function testBuildCommandUsesTitleAttribute(): void
    {
        $z3950 = new Z3950('host', '210', 'db', 'Some Title', 'Title');

        $this->assertStringContainsString(
            '"@attr 1=4 \"Some Title\"" "Some Title.mrc"',
            CommandBuilder::buildCommand($z3950)
        );
    }

    public function testBashScriptAndTemplateExist(): void
    {
        $this->assertFileExists(APP_BASH_MAIN);
        $this->assertTrue(is_executable(APP_BASH_MAIN), 'bash entrypoint must be executable');
        $this->assertFileExists(TRANSFORM_TEMPLATE_UNIMARC_TO_MARC21);
    }
}
