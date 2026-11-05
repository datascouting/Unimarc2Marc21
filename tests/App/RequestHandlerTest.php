<?php

declare(strict_types=1);

namespace Unimarc2Marc21\Tests\App;

use PHPUnit\Framework\TestCase;
use Unimarc2Marc21\App\RequestHandler;
use Unimarc2Marc21\Models\Files;

final class RequestHandlerTest extends TestCase
{
    private string $value;

    protected function setUp(): void
    {
        $this->value = 'phpunit-' . uniqid();
    }

    protected function tearDown(): void
    {
        foreach (glob(STORAGE_OUTPUT . '/' . $this->value . '.*') as $file) {
            unlink($file);
        }
    }

    public function testEmptyRequestWaits(): void
    {
        $response = RequestHandler::handle([]);

        $this->assertSame(STATUS_WAIT, $response->getStatusCode());
        $this->assertSame('Waiting for request...', $response->getMessage());
    }

    public function testSearchIsServedFromCacheWhenOutputFilesExist(): void
    {
        $files = Files::fromValue($this->value);
        file_put_contents($files->getXml(), '<record/>');
        file_put_contents($files->getMrc(), 'mrc');

        $response = RequestHandler::handle([
            'z3950_host' => 'unused.invalid',
            'z3950_port' => '210',
            'z3950_database' => 'db',
            'z3950_value' => $this->value,
            'z3950_type' => 'ISBN (Preferred)',
        ]);

        $this->assertSame(STATUS_SEARCH_OK, $response->getStatusCode());
        $this->assertSame('Found, from cache...', $response->getMessage());
        $this->assertSame($this->value, $response->getValue());
    }

    public function testImportFailsGracefullyWhenKohaIsUnreachable(): void
    {
        $this->expectErrorLog();

        file_put_contents(Files::fromValue($this->value)->getXml(), '<record/>');

        $response = RequestHandler::handle([
            'koha_intra_host' => 'http://127.0.0.1',
            'koha_intra_port' => '9',
            'koha_intra_basic_username' => '',
            'koha_intra_basic_password' => '',
            'koha_intra_username' => 'user',
            'koha_intra_password' => 'pass',
            'koha_intra_value' => $this->value,
        ]);

        $this->assertSame(STATUS_IMPORT_NOT_OK, $response->getStatusCode());
        $this->assertSame($this->value, $response->getValue());
    }

    public function testSearchWithUnsupportedTypeFails(): void
    {
        $response = RequestHandler::handle([
            'z3950_host' => 'unused.invalid',
            'z3950_port' => '210',
            'z3950_database' => 'db',
            'z3950_value' => $this->value,
            'z3950_type' => 'Author',
        ]);

        $this->assertSame(STATUS_SEARCH_NOT_OK, $response->getStatusCode());
        $this->assertSame('Unsupported type of value to find...', $response->getMessage());
    }

    public function testImportFailsGracefullyWhenRecordWasNeverConverted(): void
    {
        $this->expectErrorLog();

        $response = RequestHandler::handle([
            'koha_intra_host' => 'http://127.0.0.1',
            'koha_intra_port' => '9',
            'koha_intra_value' => $this->value,
        ]);

        $this->assertSame(STATUS_IMPORT_NOT_OK, $response->getStatusCode());
    }
}
