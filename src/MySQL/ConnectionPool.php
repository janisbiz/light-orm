<?php

namespace Janisbiz\LightOrm\MySQL;

use Janisbiz\LightOrm\MySQL\Connection\Connection;
use Janisbiz\LightOrm\MySQL\Connection\ConnectionConfig;

class ConnectionPool
{
    /**
     * @var ConnectionConfig[]
     */
    private static $connectionConfig = [];

    /**
     * @var Connection[]
     */
    private static $connections = [];

    /**
     * @param ConnectionConfig $connectionConfig
     *
     * @return $this
     */
    public function addConnectionConfig(ConnectionConfig $connectionConfig)
    {
        self::$connectionConfig[$connectionConfig->getDbname()] = $connectionConfig;

        return $this;
    }

    /**
     * @param string $dbName
     *
     * @return null|Connection
     */
    public function getConnection($dbName)
    {
        return self::getConnectionStatic($dbName);
    }

    /**
     * @param string $dbName
     *
     * @return Connection
     */
    public static function getConnectionStatic($dbName)
    {
        if (!isset(self::$connectionConfig[$dbName])) {
            throw new \InvalidArgumentException(
                empty(self::$connectionConfig)
                    ? \sprintf('Could not find connection by name "%s"!', $dbName)
                    : \sprintf(
                        'Could not find connection by name "%s"! Available connections: "%s".',
                        $dbName,
                        \implode('", "', \array_keys(self::$connectionConfig))
                    )
            );
        }

        if (!isset(self::$connections[$dbName])) {
            $connectionConfig = self::$connectionConfig[$dbName];

            if (!isset(self::$connections[$dbName])) {
                self::$connections[$dbName] = new Connection(
                    $connectionConfig->generateDsn(),
                    $connectionConfig->getUsername(),
                    $connectionConfig->getPassword()
                );
            }
        }

        return self::$connections[$dbName];
    }
}
