<?php

use PHPUnit\Framework\TestCase;

final class KohaBiblioPostTest extends TestCase
{
    public function testBuildUrlToPost(): void
    {
        $this->assertSame(
            'http://koha:8080/cgi-bin/koha/svc/new_bib?userid=user&password=pass',
            $this->invokePrivate('buildUrlToPost', ['http://koha', '8080', 'user', 'pass'])
        );
    }

    public function testBuildHeadersWithoutAuthorization(): void
    {
        $this->assertSame(
            ['Content-Type: application/xml'],
            $this->invokePrivate('buildHeaders', [''])
        );
    }

    public function testBuildHeadersWithAuthorization(): void
    {
        $this->assertSame(
            ['Content-Type: application/xml', 'Authorization: Basic dXNlcjpwYXNz'],
            $this->invokePrivate('buildHeaders', ['Basic dXNlcjpwYXNz'])
        );
    }

    public function testPostThrowsWhenKohaIsUnreachable(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'u2m');
        file_put_contents($file, '<record/>');

        try {
            $this->expectException(Exception::class);
            KohaBiblioPost::post('', '', 'http://127.0.0.1', '9', 'user', 'pass', $file);
        } finally {
            unlink($file);
        }
    }

    private function invokePrivate(string $method, array $args)
    {
        $reflection = new ReflectionMethod(KohaBiblioPost::class, $method);
        return $reflection->invokeArgs(null, $args);
    }
}
