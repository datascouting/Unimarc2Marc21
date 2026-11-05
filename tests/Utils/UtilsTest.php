<?php

declare(strict_types=1);

namespace Unimarc2Marc21\Tests\Utils;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Unimarc2Marc21\Utils\Utils;

final class UtilsTest extends TestCase
{
    public function testBuildBasicAuthHeaderValue(): void
    {
        $this->assertSame('Basic dXNlcjpwYXNz', Utils::buildBasicAuthHeaderValue('user', 'pass'));
    }

    public function testGetFileContents(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'u2m');
        file_put_contents($file, '<record/>');

        try {
            $this->assertSame('<record/>', Utils::getFileContents($file));
        } finally {
            unlink($file);
        }
    }

    public function testGetFileContentsThrowsForMissingFile(): void
    {
        $this->expectException(RuntimeException::class);
        Utils::getFileContents(sys_get_temp_dir() . '/u2m-missing-' . uniqid());
    }

    public function testGetValuesOrEmptyReturnsStringsOnly(): void
    {
        $result = Utils::getValuesOrEmpty(
            ['a' => 'x', 'b' => null, 'c' => ['array'], 'd' => 210, 'extra' => 'dropped'],
            ['a', 'b', 'c', 'd', 'e']
        );

        $this->assertSame(['a' => 'x', 'b' => '', 'c' => '', 'd' => '210', 'e' => ''], $result);
    }

    public function testGetZ3950SettingsReturnsAllSupportedKeys(): void
    {
        $settings = Utils::getZ3950Settings(['z3950_host' => 'host', 'unrelated' => 'x']);

        $this->assertSame(Utils::getZ3950SupportedValues(), array_keys($settings));
        $this->assertSame('host', $settings['z3950_host']);
        $this->assertSame('', $settings['z3950_port']);
    }

    public function testGetKohaSettingsReturnsAllSupportedKeys(): void
    {
        $settings = Utils::getKohaSettings(['koha_intra_port' => '8080']);

        $this->assertSame(Utils::getKohaSupportedValues(), array_keys($settings));
        $this->assertSame('8080', $settings['koha_intra_port']);
        $this->assertSame('', $settings['koha_intra_host']);
    }

    public function testAreAllValuesEmpty(): void
    {
        $this->assertTrue(Utils::areAllValuesEmpty(['a' => '', 'b' => ''], ['a', 'b']));
        $this->assertTrue(Utils::areAllValuesEmpty([], ['a', 'b']));
        $this->assertFalse(Utils::areAllValuesEmpty(['a' => '', 'b' => 'x'], ['a', 'b']));
        $this->assertFalse(Utils::areAllValuesEmpty(['a' => '0'], ['a']));
        $this->assertTrue(Utils::areAllValuesEmpty(['a' => '', 'b' => 'x'], ['a']));
    }

    #[DataProvider('requestValueProvider')]
    public function testIsSearchAndConvertRequest(array $request, bool $expected): void
    {
        $this->assertSame($expected, Utils::isSearchAndConvertRequest(self::withKey($request, 'z3950_value')));
    }

    #[DataProvider('requestValueProvider')]
    public function testIsImportToKohaRequest(array $request, bool $expected): void
    {
        $this->assertSame($expected, Utils::isImportToKohaRequest(self::withKey($request, 'koha_intra_value')));
    }

    public static function requestValueProvider(): array
    {
        return [
            'missing' => [[], false],
            'empty'   => [['value' => ''], false],
            'null'    => [['value' => null], false],
            'array'   => [['value' => ['x']], false],
            'set'     => [['value' => '9789600000000'], true],
        ];
    }

    public function testAddDefaultsOverwritesListedKeysAndKeepsOthers(): void
    {
        $result = Utils::addDefaults(
            ['a' => 'old', 'keep' => 'me'],
            ['a', 'b', 'c'],
            ['a' => 'new', 'b' => 'default-b']
        );

        $this->assertSame(['a' => 'new', 'keep' => 'me', 'b' => 'default-b', 'c' => ''], $result);
    }

    public function testGetRelatedFilePathStripsInstallationPath(): void
    {
        $this->assertSame(
            '/storage/output/x.xml',
            Utils::getRelatedFilePath('/var/www/html', '/var/www/html/storage/output/x.xml')
        );
    }

    #[DataProvider('fileNameProvider')]
    public function testToFileName(string $value, string $expected): void
    {
        $this->assertSame($expected, Utils::toFileName($value));
    }

    public static function fileNameProvider(): array
    {
        return [
            'isbn with dashes' => ['978-960-044-259-5', '978-960-044-259-5'],
            'spaces'           => ['Some Title', 'Some_Title'],
            'greek is kept'    => ['Η Οδύσσεια', 'Η_Οδύσσεια'],
            'path traversal'   => ['../../config.php', '_.._config.php'],
            'leading dots'     => ['..hidden', 'hidden'],
            'only dots'        => ['..', '_'],
            'shell chars'      => ['$(id); rm -rf /', '_id_rm_-rf_'],
            'empty'            => ['', '_'],
        ];
    }

    public function testToFileNameIsLimitedAndKeepsValidUtf8(): void
    {
        $name = Utils::toFileName(str_repeat('Ω', 200));

        $this->assertLessThanOrEqual(150, strlen($name));
        $this->assertTrue(mb_check_encoding($name, 'UTF-8'));
    }

    public function testToUrlPathEncodesSegments(): void
    {
        $this->assertSame(
            '/storage/output/Some%20Title%23.mrc.marc21.xml',
            Utils::toUrlPath('/storage/output/Some Title#.mrc.marc21.xml')
        );
    }

    public function testEscape(): void
    {
        $this->assertSame('&lt;script&gt;&quot;&#039;&amp;', Utils::escape('<script>"\'&'));
    }

    private static function withKey(array $request, string $key): array
    {
        return array_key_exists('value', $request) ? [$key => $request['value']] : [];
    }
}
