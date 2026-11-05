<?php

declare(strict_types=1);

namespace Unimarc2Marc21\Tests\Models;

use PHPUnit\Framework\TestCase;
use Unimarc2Marc21\Enums\QueryType;
use Unimarc2Marc21\Models\Files;
use Unimarc2Marc21\Models\Koha;
use Unimarc2Marc21\Models\Response;
use Unimarc2Marc21\Models\Z3950;

final class ModelsTest extends TestCase
{
    public function testZ3950FromRequest(): void
    {
        $z3950 = Z3950::fromRequest([
            'z3950_host' => 'host',
            'z3950_port' => '210',
            'z3950_database' => 'db',
            'z3950_value' => '123',
            'z3950_type' => 'ISSN',
        ]);

        $this->assertSame('host', $z3950->getHost());
        $this->assertSame('210', $z3950->getPort());
        $this->assertSame('db', $z3950->getDatabase());
        $this->assertSame('123', $z3950->getValue());
        $this->assertSame('ISSN', $z3950->getType());
        $this->assertSame(QueryType::Issn, $z3950->getQueryType());
    }

    public function testZ3950FromIncompleteRequestDoesNotCrash(): void
    {
        $z3950 = Z3950::fromRequest(['z3950_value' => ['not', 'a', 'string']]);

        $this->assertSame('', $z3950->getValue());
        $this->assertSame('', $z3950->getHost());
        $this->assertNull($z3950->getQueryType());
    }

    public function testKohaFromRequest(): void
    {
        $koha = Koha::fromRequest([
            'koha_intra_host' => 'http://koha',
            'koha_intra_port' => '8080',
            'koha_intra_basic_username' => 'bu',
            'koha_intra_basic_password' => 'bp',
            'koha_intra_username' => 'u',
            'koha_intra_password' => 'p',
            'koha_intra_value' => '123',
        ]);

        $this->assertSame('http://koha', $koha->getHost());
        $this->assertSame('8080', $koha->getPort());
        $this->assertSame('bu', $koha->getBasicAuthUsername());
        $this->assertSame('bp', $koha->getBasicAuthPassword());
        $this->assertSame('u', $koha->getUsername());
        $this->assertSame('p', $koha->getPassword());
        $this->assertSame('123', $koha->getValue());
    }

    public function testKohaFromIncompleteRequestDoesNotCrash(): void
    {
        $koha = Koha::fromRequest(['koha_intra_value' => '123']);

        $this->assertSame('123', $koha->getValue());
        $this->assertSame('', $koha->getHost());
    }

    public function testResponse(): void
    {
        $waiting = new Response(STATUS_WAIT, 'waiting');
        $this->assertSame('', $waiting->getValue());
        $this->assertNull($waiting->getXml());

        $response = Response::withValue(STATUS_SEARCH_OK, 'ok', '123');
        $this->assertSame(STATUS_SEARCH_OK, $response->getStatusCode());
        $this->assertSame('ok', $response->getMessage());
        $this->assertSame('123', $response->getValue());

        $response = Response::withValueAndXml(STATUS_IMPORT_OK, 'imported', '123', '<xml/>');
        $this->assertSame(STATUS_IMPORT_OK, $response->getStatusCode());
        $this->assertSame('123', $response->getValue());
        $this->assertSame('<xml/>', $response->getXml());
    }

    public function testFilesFromValueBuildsOutputPaths(): void
    {
        $files = Files::fromValue('978-960-044-259-5');

        $this->assertSame('978-960-044-259-5', $files->getValue());
        $this->assertSame(STORAGE_OUTPUT . '/978-960-044-259-5.mrc.marc21.xml', $files->getXml());
        $this->assertSame(STORAGE_OUTPUT . '/978-960-044-259-5.mrc.marc21.mrc', $files->getMrc());
    }

    public function testFilesFromValueStaysInsideOutputStorage(): void
    {
        $files = Files::fromValue('../../config.php');

        $this->assertSame(STORAGE_OUTPUT, dirname($files->getXml()));
        $this->assertSame(STORAGE_OUTPUT, dirname($files->getMrc()));
    }

    public function testNoFiles(): void
    {
        $files = Files::none();

        $this->assertFalse($files->existsBoth());
        $this->assertSame('', $files->getXmlContents());
    }

    public function testFilesExistsBothAndXmlContents(): void
    {
        $dir = sys_get_temp_dir();
        $xml = tempnam($dir, 'u2m');
        $mrc = $dir . '/u2m-missing-' . uniqid() . '.mrc';
        file_put_contents($xml, '<record/>');

        try {
            $files = new Files('v', $xml, $mrc);
            $this->assertFalse($files->existsBoth());
            $this->assertSame('', $files->getXmlContents());

            touch($mrc);
            $this->assertTrue($files->existsBoth());
            $this->assertSame('<record/>', $files->getXmlContents());
        } finally {
            @unlink($xml);
            @unlink($mrc);
        }
    }
}
