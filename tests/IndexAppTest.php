<?php

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class IndexAppTest extends TestCase
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
        $response = getResponse([]);

        $this->assertSame(STATUS_WAIT, $response->getStatusCode());
        $this->assertSame('Waiting for request...', $response->getMessage());
    }

    public function testSearchIsServedFromCacheWhenOutputFilesExist(): void
    {
        $files = Files::fromvalue($this->value);
        file_put_contents($files->getXml(), '<record/>');
        file_put_contents($files->getMrc(), 'mrc');

        $response = getResponse([
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
        file_put_contents(Files::fromvalue($this->value)->getXml(), '<record/>');

        $response = getResponse([
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

    #[DataProvider('visibilityProvider')]
    public function testObjectVisibility(int $status, bool $hidden): void
    {
        $response = new Response($status, '');

        $this->assertSame($hidden, mustHideObjects($response));
        $this->assertSame($hidden ? 'd-none' : '', getHideClassOrEmpty($response));
    }

    public static function visibilityProvider(): array
    {
        return [
            'wait'          => [STATUS_WAIT, true],
            'search ok'     => [STATUS_SEARCH_OK, false],
            'search not ok' => [STATUS_SEARCH_NOT_OK, true],
            'import ok'     => [STATUS_IMPORT_OK, false],
            'import not ok' => [STATUS_IMPORT_NOT_OK, false],
        ];
    }
}
