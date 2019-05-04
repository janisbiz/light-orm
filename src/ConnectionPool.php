<?php

namespace Janisbiz\LightOrm;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Dms\MySQL\Connection\Connection;
use Janisbiz\LightOrm\Connection\ConnectionConfig;

class ConnectionPool
{
    /**
     * @var ConnectionConfig[]
     */
    protected static $connectionConfig = [];

    /**
     * @var Connection[]
     */
    protected static $connections = [];

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
        if (empty(self::$connectionConfig[$dbName])) {
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

        $connectionConfig = self::$connectionConfig[$dbName];

        if (!isset(self::$connections[$dbName])) {
            self::$connections[$dbName] = $this->createConnection($connectionConfig);
        }

        return self::$connections[$dbName];
    }

    /**
     * @param string $dbName
     *
     * @return Connection
     */
    public static function getConnectionStatic($dbName)
    {
        return (new self())->getConnection($dbName);
    }

    /**
     * @param ConnectionConfig $connectionConfig
     *
     * @return ConnectionInterface
     */
    protected function createConnection(ConnectionConfig $connectionConfig)
    {
        $adapterConnectionClass = $connectionConfig->getAdapterConnectionClass();

        return new $adapterConnectionClass(
            $connectionConfig->generateDsn(),
            $connectionConfig->getUsername(),
            $connectionConfig->getPassword()
        );
    }
}
