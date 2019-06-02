<?php

namespace Janisbiz\LightOrm\Connection;

abstract class AbstractConnectionConfig implements ConnectionConfigInterface
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $dbname;

    /**
     * @var string
     */
    protected $adapter;

    /**
     * @var int
     */
    protected $port;

    /**
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $dbname
     * @param string $adapter
     * @param int $port
     */
    public function __construct($host, $username, $password, $dbname, $adapter, $port = 3306)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
        $this->adapter = $adapter;
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getDbname()
    {
        return $this->dbname;
    }
}
