<?php


class Z3950
{
    private $host;
    private $port;
    private $database;
    private $value;
    private $type;

    public static function fromRequest($request): Z3950
    {
        return new Z3950(
            $request['z3950_host'],
            $request['z3950_port'],
            $request['z3950_database'],
            $request['z3950_value'],
            $request['z3950_type']
        );
    }


    /**
     * Z3950 constructor.
     * @param string $host
     * @param string $port
     * @param string $database
     * @param string $value
     * @param string $type
     */
    public function __construct(string $host, string $port, string $database, string $value, string $type)
    {
        $this->host = $host;
        $this->port = $port;
        $this->database = $database;
        $this->value = $value;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host)
    {
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getPort(): string
    {
        return $this->port;
    }

    /**
     * @param string $port
     */
    public function setPort(string $port)
    {
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getDatabase(): string
    {
        return $this->database;
    }

    /**
     * @param string $database
     */
    public function setDatabase(string $database)
    {
        $this->database = $database;
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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

}