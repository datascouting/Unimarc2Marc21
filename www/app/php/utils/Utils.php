<?php

abstract class Utils
{

    /**
     * @param string $filePath
     * @param string $fileName
     * @return string
     */
    public static function getFullFilePath(string $filePath, string $fileName): string
    {
        return $filePath . '/' . $fileName;
    }

    /**
     * @param string $username
     * @param string $password
     * @return string
     */
    public static function buildBasicAuthHeaderValue(string $username, string $password): string
    {
        return "Basic " . base64_encode($username . ":" . $password);
    }


    /**
     * @param string $fullFilePath
     * @return string
     */
    public static function getFileContents(string $fullFilePath): string
    {
        return file_get_contents($fullFilePath);
    }

    /**
     * @return array
     */
    public static function getKohaSupportedValues()
    {
        return [
            'koha_intra_host',
            'koha_intra_port',
            'koha_intra_basic_username',
            'koha_intra_basic_password',
            'koha_intra_username',
            'koha_intra_password',
            "koha_intra_value",
            "koha_intra_marc21_xml"
        ];
    }

    /**
     * @return array
     */
    public static function getZ3950SupporterValues()
    {
        return [
            'z3950_host',
            'z3950_port',
            'z3950_database',
            'z3950_value',
            'z3950_type'
        ];
    }

    /**
     * @param $object
     * @return array
     */
    public static function getKohaSettings($object)
    {
        return Utils::getValuesOrEmpty(
            $object,
            Utils::getKohaSupportedValues()
        );
    }

    /**
     * @param $object
     * @return array
     */
    public static function getZ3950Settings($object)
    {
        return Utils::getValuesOrEmpty(
            $object,
            Utils::getZ3950SupporterValues()
        );
    }

    /**
     * @param $object
     * @param array $values
     * @return array
     */
    public static function getValuesOrEmpty($object, array $values)
    {
        $valuesToReturn = [];

        foreach ($values as $value) {
            if (!(isset($object[$value]))) {
                $valuesToReturn[$value] = "";
                continue;
            }

            $valuesToReturn[$value] = $object[$value];
        }

        return $valuesToReturn;
    }

    /**
     * @param $object
     * @param array $values
     * @return bool
     */
    public static function areAllValuesEmpty($object, array $values)
    {
        foreach ($values as $value) {
            if ($object[$value] != "") {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $object
     * @return bool
     */
    public static function isSearchAndConvertRequest($object)
    {
        return isset($object['z3950_value']) && ($object['z3950_value'] != '');
    }

    /**
     * @param $object
     * @return bool
     */
    public static function isImportToKohaRequest($object)
    {
        return isset($object['koha_intra_value']) && ($object['koha_intra_value'] != '');
    }

    /**
     * @param $object
     * @param $values
     * @param $defaultValues
     * @return mixed
     */
    public static function addDefaults($object, $values, $defaultValues)
    {
        foreach ($values as $value) {
            if (!(isset($defaultValues[$value]))) {
                $object[$value] = '';
                continue;
            }

            $object[$value] = $defaultValues[$value];
        }

        return $object;
    }

    /**
     * @param string $installationPath
     * @param string $fullFilePath
     * @return string
     */
    public static function getRelatedFilePath(string $installationPath, string $fullFilePath): string
    {
        return substr($fullFilePath, strlen($installationPath));
    }
}
