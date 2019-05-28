<?php

namespace Janisbiz\LightOrm\Dms\MySQL\Connection;

use Janisbiz\LightOrm\Connection\AbstractConnectionConfig;

class ConnectionConfig extends AbstractConnectionConfig
{
    const ADAPTER = 'mysql';

    /**
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $dbname
     * @param int $port
     */
    public function __construct($host, $username, $password, $dbname, $port = 3306)
    {
        parent::__construct($host, $username, $password, $dbname, static::ADAPTER, $port);
    }

    /**
     * @return string
     */
    public function generateDsn()
    {
        return \sprintf(
            '%s:host=%s;dbname=%s;charset=utf8mb4;port=%d',
            $this->adapter,
            $this->host,
            $this->dbname,
            $this->port
        );
    }

    /**
     * @return string
     */
    public function getAdapterConnectionClass()
    {
        return Connection::class;
    }
}
