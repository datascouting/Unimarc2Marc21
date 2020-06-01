<?php


class Koha
{
    private $host;
    private $port;
    private $basicAuthUsername;
    private $basicAuthPassword;
    private $username;
    private $password;
    private $value;

    public static function fromRequest($request): Koha
    {
        return new Koha(
            $request['koha_intra_host'],
            $request['koha_intra_port'],
            $request['koha_intra_basic_username'],
            $request['koha_intra_basic_password'],
            $request['koha_intra_username'],
            $request['koha_intra_password'],
            $request['koha_intra_value']
        );
    }

    /**
     * Koha constructor.
     * @param string $host
     * @param string $port
     * @param string $basicAuthUsername
     * @param string $basicAuthPassword
     * @param string $username
     * @param string $password
     * @param string $value
     */
    public function __construct(string $host,
                                string $port,
                                string $basicAuthUsername,
                                string $basicAuthPassword,
                                string $username,
                                string $password,
                                string $value)
    {
        $this->host = $host;
        $this->port = $port;
        $this->basicAuthUsername = $basicAuthUsername;
        $this->basicAuthPassword = $basicAuthPassword;
        $this->username = $username;
        $this->password = $password;
        $this->value = $value;
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
    public function getBasicAuthUsername(): string
    {
        return $this->basicAuthUsername;
    }

    /**
     * @param string $basicAuthUsername
     */
    public function setBasicAuthUsername(string $basicAuthUsername)
    {
        $this->basicAuthUsername = $basicAuthUsername;
    }

    /**
     * @return string
     */
    public function getBasicAuthPassword(): string
    {
        return $this->basicAuthPassword;
    }

    /**
     * @param string $basicAuthPassword
     */
    public function setBasicAuthPassword(string $basicAuthPassword)
    {
        $this->basicAuthPassword = $basicAuthPassword;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
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
}