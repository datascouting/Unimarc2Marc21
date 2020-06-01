<?php


class Response
{
    private $statusCode;
    private $message;
    private $value;
    private $xml;

    /**
     * Response constructor.
     * @param int $statusCode
     * @param string $message
     */
    public function __construct(int $statusCode, string $message)
    {
        $this->statusCode = $statusCode;
        $this->message = $message;
    }

    /**
     * @param int $statusCode
     * @param string $message
     * @param string $value
     * @return Response
     */
    public static function withvalue(int $statusCode, string $message, string $value): Response
    {
        $response = new Response($statusCode, $message);
        $response->setValue($value);

        return $response;
    }

    public static function withvalueAndXML(int $statusCode, string $message, string $value, string $xml): Response
    {
        $response = Response::withvalue($statusCode, $message, $value);
        $response->setXml($xml);

        return $response;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
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
     * @return mixed
     */
    public function getXml()
    {
        return $this->xml;
    }

    /**
     * @param mixed $xml
     */
    public function setXml($xml)
    {
        $this->xml = $xml;
    }
}