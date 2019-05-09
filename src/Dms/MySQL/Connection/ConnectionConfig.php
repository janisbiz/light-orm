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
     */
    public function __construct($host, $username, $password, $dbname)
    {
        parent::__construct($host, $username, $password, $dbname, self::ADAPTER);
    }

    /**
     * @return string
     */
    public function generateDsn()
    {
        return \sprintf(
            '%s:host=%s;dbname=%s;charset=utf8mb4',
            $this->adapter,
            $this->host,
            $this->dbname
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
