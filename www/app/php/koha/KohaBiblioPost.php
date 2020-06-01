<?php

require_once APP_PHP_PATH . "/utils/Utils.php";

abstract class KohaBiblioPost
{

    /**
     * @param string $host
     * @param string $port
     * @param string $userId
     * @param string $password
     * @return string
     */
    private static function buildUrlToPost(string $host, string $port, string $userId, string $password): string
    {
        return sprintf("%s:%s/cgi-bin/koha/svc/new_bib?userid=%s&password=%s", $host, $port, $userId, $password);
    }

    /**
     * @param string $basicAuthHeaderValue
     * @return array
     */
    private static function buildHeaders(string $basicAuthHeaderValue): array
    {
        $headers = array();

        array_push($headers, 'Content-Type: application/xml');

        if ($basicAuthHeaderValue !== "") {
            array_push($headers, sprintf("Authorization: %s", $basicAuthHeaderValue));
        }

        return $headers;
    }


    /**
     * @param string $basicAuthUsername
     * @param string $basicAuthPassword
     * @param string $kohaHost
     * @param string $kohaPort
     * @param string $kohaUsername
     * @param string $kohaPassword
     * @param string $marc21XMLFile
     * @return string
     * @throws Exception
     */
    public static function post(string $basicAuthUsername,
                                string $basicAuthPassword,
                                string $kohaHost,
                                string $kohaPort,
                                string $kohaUsername,
                                string $kohaPassword,
                                string $marc21XMLFile): string
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::buildUrlToPost($kohaHost, $kohaPort, $kohaUsername, $kohaPassword),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => Utils::getFileContents($marc21XMLFile),
            CURLOPT_HTTPHEADER => self::buildHeaders(Utils::buildBasicAuthHeaderValue($basicAuthUsername, $basicAuthPassword)),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($err) {
            throw new Exception($err);
        }

        if ($httpcode !== 200) {
            throw new Exception(sprintf(
                "Can't import to Koha!!! Status Code=%s",
                $httpcode
            ));
        }

        return $response;
    }
}