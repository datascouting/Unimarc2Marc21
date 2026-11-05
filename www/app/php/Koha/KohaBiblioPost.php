<?php

declare(strict_types=1);

namespace Unimarc2Marc21\Koha;

use RuntimeException;
use Unimarc2Marc21\Models\Koha;
use Unimarc2Marc21\Utils\Utils;

final class KohaBiblioPost
{
    private function __construct()
    {
    }

    public static function buildUrl(string $host, string $port, string $userId, string $password): string
    {
        return sprintf(
            "%s:%s/cgi-bin/koha/svc/new_bib?%s",
            rtrim($host, '/'),
            $port,
            http_build_query(['userid' => $userId, 'password' => $password])
        );
    }

    /**
     * @return list<string>
     */
    public static function buildHeaders(string $basicAuthUsername, string $basicAuthPassword): array
    {
        $headers = ['Content-Type: application/xml'];

        if ($basicAuthUsername !== '' || $basicAuthPassword !== '') {
            $headers[] = 'Authorization: ' . Utils::buildBasicAuthHeaderValue($basicAuthUsername, $basicAuthPassword);
        }

        return $headers;
    }

    /**
     * Posts a MARC21 XML record to Koha's new_bib service and returns Koha's response body.
     *
     * @throws RuntimeException when the file can't be read, the request fails or Koha doesn't answer 200
     */
    public static function post(Koha $koha, string $marc21XmlFile): string
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => self::buildUrl($koha->getHost(), $koha->getPort(), $koha->getUsername(), $koha->getPassword()),
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => Utils::getFileContents($marc21XmlFile),
            CURLOPT_HTTPHEADER => self::buildHeaders($koha->getBasicAuthUsername(), $koha->getBasicAuthPassword()),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        ]);

        $response = curl_exec($curl);

        if ($response === false) {
            throw new RuntimeException(curl_error($curl));
        }

        $httpCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

        if ($httpCode !== 200) {
            throw new RuntimeException(sprintf("Can't import to Koha!!! Status Code=%s", $httpCode));
        }

        return (string) $response;
    }
}
