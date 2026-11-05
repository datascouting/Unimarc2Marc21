<?php

declare(strict_types=1);

namespace Unimarc2Marc21\Tests;

use PHPUnit\Framework\TestCase;

final class DefaultsTest extends TestCase
{
    protected function tearDown(): void
    {
        unset($_GET['username'], $_GET['password']);
    }

    public function testDefaultZ3950CoversAllRequiredKeys(): void
    {
        $defaults = getDefaultZ3950();

        foreach (['z3950_host', 'z3950_port', 'z3950_database', 'z3950_type'] as $key) {
            $this->assertArrayHasKey($key, $defaults);
            $this->assertNotSame('', $defaults[$key], "$key must have a default");
        }
    }

    public function testDefaultKohaCredentialsAreEmptyWithoutQueryParams(): void
    {
        $defaults = getDefaultKoha();

        $this->assertSame('', $defaults['koha_intra_username']);
        $this->assertSame('', $defaults['koha_intra_password']);
    }

    public function testDefaultKohaCredentialsComeFromQueryParams(): void
    {
        $_GET['username'] = 'librarian';
        $_GET['password'] = 'secret';

        $defaults = getDefaultKoha();

        $this->assertSame('librarian', $defaults['koha_intra_username']);
        $this->assertSame('secret', $defaults['koha_intra_password']);
    }

    public function testArrayQueryParamsAreIgnored(): void
    {
        $_GET['username'] = ['x'];

        $this->assertSame('', getDefaultKoha()['koha_intra_username']);
    }
}
