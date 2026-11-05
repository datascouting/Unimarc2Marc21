<?php

declare(strict_types=1);

namespace Unimarc2Marc21\Tests\Koha;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Unimarc2Marc21\Koha\KohaBiblioPost;
use Unimarc2Marc21\Models\Koha;

final class KohaBiblioPostTest extends TestCase
{
    public function testBuildUrl(): void
    {
        $this->assertSame(
            'http://koha:8080/cgi-bin/koha/svc/new_bib?userid=user&password=pass',
            KohaBiblioPost::buildUrl('http://koha/', '8080', 'user', 'pass')
        );
    }

    public function testBuildUrlEncodesCredentials(): void
    {
        $this->assertSame(
            'http://koha:80/cgi-bin/koha/svc/new_bib?userid=a%26b&password=p%3Dq%23r',
            KohaBiblioPost::buildUrl('http://koha', '80', 'a&b', 'p=q#r')
        );
    }

    public function testNoAuthorizationHeaderWithoutBasicCredentials(): void
    {
        $this->assertSame(['Content-Type: application/xml'], KohaBiblioPost::buildHeaders('', ''));
    }

    public function testAuthorizationHeaderWithBasicCredentials(): void
    {
        $this->assertSame(
            ['Content-Type: application/xml', 'Authorization: Basic dXNlcjpwYXNz'],
            KohaBiblioPost::buildHeaders('user', 'pass')
        );
    }

    public function testPostThrowsWhenKohaIsUnreachable(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'u2m');
        file_put_contents($file, '<record/>');

        try {
            $this->expectException(RuntimeException::class);
            KohaBiblioPost::post(new Koha('http://127.0.0.1', '9', '', '', 'user', 'pass', '1'), $file);
        } finally {
            unlink($file);
        }
    }

    public function testPostThrowsWhenRecordFileIsMissing(): void
    {
        $this->expectException(RuntimeException::class);
        KohaBiblioPost::post(
            new Koha('http://127.0.0.1', '9', '', '', 'user', 'pass', '1'),
            sys_get_temp_dir() . '/u2m-missing-' . uniqid()
        );
    }
}
