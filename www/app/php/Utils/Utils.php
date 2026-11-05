<?php

declare(strict_types=1);

namespace Unimarc2Marc21\Utils;

use RuntimeException;

final class Utils
{
    private const array KOHA_KEYS = [
        'koha_intra_host',
        'koha_intra_port',
        'koha_intra_basic_username',
        'koha_intra_basic_password',
        'koha_intra_username',
        'koha_intra_password',
        'koha_intra_value',
    ];

    private const array Z3950_KEYS = [
        'z3950_host',
        'z3950_port',
        'z3950_database',
        'z3950_value',
        'z3950_type',
    ];

    private function __construct()
    {
    }

    public static function buildBasicAuthHeaderValue(string $username, string $password): string
    {
        return "Basic " . base64_encode($username . ":" . $password);
    }

    /**
     * @throws RuntimeException when the file cannot be read
     */
    public static function getFileContents(string $fullFilePath): string
    {
        $contents = is_file($fullFilePath) ? file_get_contents($fullFilePath) : false;

        if ($contents === false) {
            throw new RuntimeException(sprintf("Can't read file %s", $fullFilePath));
        }

        return $contents;
    }

    /**
     * @return list<string>
     */
    public static function getKohaSupportedValues(): array
    {
        return self::KOHA_KEYS;
    }

    /**
     * @return list<string>
     */
    public static function getZ3950SupportedValues(): array
    {
        return self::Z3950_KEYS;
    }

    /**
     * @return array<string, string>
     */
    public static function getKohaSettings(array $request): array
    {
        return self::getValuesOrEmpty($request, self::KOHA_KEYS);
    }

    /**
     * @return array<string, string>
     */
    public static function getZ3950Settings(array $request): array
    {
        return self::getValuesOrEmpty($request, self::Z3950_KEYS);
    }

    /**
     * Picks the given keys from the request as strings; missing or non-scalar values become "".
     *
     * @param list<string> $keys
     * @return array<string, string>
     */
    public static function getValuesOrEmpty(array $request, array $keys): array
    {
        $values = [];

        foreach ($keys as $key) {
            $values[$key] = self::stringValue($request, $key);
        }

        return $values;
    }

    /**
     * Reads a request value as a string; missing or non-scalar values (e.g. `name[]=x`) become "".
     */
    public static function stringValue(array $request, string $key): string
    {
        $value = $request[$key] ?? '';

        return is_scalar($value) ? (string) $value : '';
    }

    /**
     * @param list<string> $keys
     */
    public static function areAllValuesEmpty(array $values, array $keys): bool
    {
        return array_all($keys, fn($key) => self::stringValue($values, $key) === '');

    }

    public static function isSearchAndConvertRequest(array $request): bool
    {
        return self::stringValue($request, 'z3950_value') !== '';
    }

    public static function isImportToKohaRequest(array $request): bool
    {
        return self::stringValue($request, 'koha_intra_value') !== '';
    }

    /**
     * Sets every listed key from the defaults ("" when there is no default), keeping other keys.
     *
     * @param list<string> $keys
     * @param array<string, string> $defaults
     */
    public static function addDefaults(array $request, array $keys, array $defaults): array
    {
        foreach ($keys as $key) {
            $request[$key] = $defaults[$key] ?? '';
        }

        return $request;
    }

    public static function getRelatedFilePath(string $installationPath, string $fullFilePath): string
    {
        return substr($fullFilePath, strlen($installationPath));
    }

    /**
     * Turns a search value (ISBN, ISSN, title...) into a safe file name: letters (any script),
     * digits, dot, dash and underscore only, no leading dots, at most 150 bytes.
     */
    public static function toFileName(string $value): string
    {
        $name = preg_replace('/[^\p{L}\p{N}._-]+/u', '_', $value) ?? '';
        $name = ltrim(mb_strcut($name, 0, 150, 'UTF-8'), '.');

        return $name === '' ? '_' : $name;
    }

    /**
     * URL-encodes each segment of a relative path (keeps the slashes).
     */
    public static function toUrlPath(string $path): string
    {
        return explode('/', $path)
                |> (fn($x) => array_map('rawurlencode', $x))
                |> (fn($x) => implode('/', $x));
    }

    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
