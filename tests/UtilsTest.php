<?php

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UtilsTest extends TestCase
{
    public function testGetFullFilePathJoinsWithSlash(): void
    {
        $this->assertSame('/a/b/file.xml', Utils::getFullFilePath('/a/b', 'file.xml'));
    }

    public function testBuildBasicAuthHeaderValue(): void
    {
        $this->assertSame('Basic dXNlcjpwYXNz', Utils::buildBasicAuthHeaderValue('user', 'pass'));
    }

    public function testBuildBasicAuthHeaderValueWithEmptyCredentials(): void
    {
        $this->assertSame('Basic Og==', Utils::buildBasicAuthHeaderValue('', ''));
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

    public function testGetValuesOrEmptyKeepsPresentAndFillsMissing(): void
    {
        $result = Utils::getValuesOrEmpty(
            ['a' => 'x', 'b' => null, 'extra' => 'dropped'],
            ['a', 'b', 'c']
        );

        $this->assertSame(['a' => 'x', 'b' => '', 'c' => ''], $result);
    }

    public function testGetZ3950SettingsReturnsAllSupportedKeys(): void
    {
        $settings = Utils::getZ3950Settings(['z3950_host' => 'host', 'unrelated' => 'x']);

        $this->assertSame(Utils::getZ3950SupporterValues(), array_keys($settings));
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
        $this->assertFalse(Utils::areAllValuesEmpty(['a' => '', 'b' => 'x'], ['a', 'b']));
        $this->assertFalse(Utils::areAllValuesEmpty(['a' => '0'], ['a']));
        $this->assertTrue(Utils::areAllValuesEmpty(['a' => '', 'b' => 'x'], ['a']));
    }

    #[DataProvider('requestValueProvider')]
    public function testIsSearchAndConvertRequest(array $request, bool $expected): void
    {
        $this->assertSame($expected, Utils::isSearchAndConvertRequest(
            $this->renameKey($request, 'z3950_value')
        ));
    }

    #[DataProvider('requestValueProvider')]
    public function testIsImportToKohaRequest(array $request, bool $expected): void
    {
        $this->assertSame($expected, Utils::isImportToKohaRequest(
            $this->renameKey($request, 'koha_intra_value')
        ));
    }

    public static function requestValueProvider(): array
    {
        return [
            'missing' => [[], false],
            'empty'   => [['value' => ''], false],
            'null'    => [['value' => null], false],
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

        $this->assertSame(
            ['a' => 'new', 'keep' => 'me', 'b' => 'default-b', 'c' => ''],
            $result
        );
    }

    public function testGetRelatedFilePathStripsInstallationPath(): void
    {
        $this->assertSame(
            '/storage/output/x.xml',
            Utils::getRelatedFilePath('/var/www/html', '/var/www/html/storage/output/x.xml')
        );
    }

    private function renameKey(array $request, string $key): array
    {
        return array_key_exists('value', $request) ? [$key => $request['value']] : [];
    }
}
