<?php

class Files
{
    private $value;
    private $xml;
    private $mrc;

    /**
     * @param string $value
     * @return Files
     */
    public static function fromvalue(string $value): Files
    {
        $fileToDownloadXML = sprintf("%s/%s", STORAGE_OUTPUT, sprintf("%s.mrc.marc21.xml", $value));
        $fileToDownloadMRC = sprintf("%s/%s", STORAGE_OUTPUT, sprintf("%s.mrc.marc21.mrc", $value));

        return new Files($value, $fileToDownloadXML, $fileToDownloadMRC);
    }

    /**
     * Files constructor.
     * @param string $value
     * @param string $xml
     * @param string $mrc
     */
    public function __construct(string $value, string $xml, string $mrc)
    {
        $this->value = $value;
        $this->xml = $xml;
        $this->mrc = $mrc;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getXml(): string
    {
        return $this->xml;
    }

    /**
     * @param string $xml
     */
    public function setXml(string $xml)
    {
        $this->xml = $xml;
    }

    /**
     * @return string
     */
    public function getMrc(): string
    {
        return $this->mrc;
    }

    /**
     * @param string $mrc
     */
    public function setMrc(string $mrc)
    {
        $this->mrc = $mrc;
    }

    /**
     * @return bool
     */
    public function existsBoth(): bool
    {
        return file_exists($this->getXml()) && file_exists($this->getMrc());
    }

    /**
     * @return string
     */
    public function getXmlContents(): string
    {
        if (!($this->existsBoth())) {
            return "";
        }

        return file_get_contents($this->getXml());
    }
}