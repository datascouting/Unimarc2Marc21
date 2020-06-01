<?php

abstract class QueryType
{
    /**
     * Get query type mapping
     * @return string[]
     */
    public static function getTypes()
    {
        return array(
            "ISBN (Preferred)" => "1=7",
            "ISSN" => "1=8",
            "Title" => "1=4"
        );
    }


    /**
     * Convert human-readable type to query type
     * @param $type
     * @return string
     */
    public static function toQuery($type)
    {
        return QueryType::getTypes()[$type];
    }
}