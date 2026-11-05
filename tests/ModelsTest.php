<?php

use PHPUnit\Framework\TestCase;

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
    }

    public function testZ3950Setters(): void
    {
        $z3950 = new Z3950('', '', '', '', '');
        $z3950->setHost('h');
        $z3950->setPort('p');
        $z3950->setDatabase('d');
        $z3950->setValue('v');
        $z3950->setType('t');

        $this->assertSame(['h', 'p', 'd', 'v', 't'], [
            $z3950->getHost(), $z3950->getPort(), $z3950->getDatabase(), $z3950->getValue(), $z3950->getType(),
        ]);
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

    public function testResponseFactories(): void
    {
        $response = Response::withvalue(STATUS_SEARCH_OK, 'ok', '123');
        $this->assertSame(STATUS_SEARCH_OK, $response->getStatusCode());
        $this->assertSame('ok', $response->getMessage());
        $this->assertSame('123', $response->getValue());
        $this->assertNull($response->getXml());

        $response = Response::withvalueAndXML(STATUS_IMPORT_OK, 'imported', '123', '<xml/>');
        $this->assertSame(STATUS_IMPORT_OK, $response->getStatusCode());
        $this->assertSame('123', $response->getValue());
        $this->assertSame('<xml/>', $response->getXml());
    }

    public function testFilesFromValueBuildsOutputPaths(): void
    {
        $files = Files::fromvalue('123');

        $this->assertSame('123', $files->getValue());
        $this->assertSame(STORAGE_OUTPUT . '/123.mrc.marc21.xml', $files->getXml());
        $this->assertSame(STORAGE_OUTPUT . '/123.mrc.marc21.mrc', $files->getMrc());
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
