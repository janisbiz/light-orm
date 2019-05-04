<?php

namespace Janisbiz\LightOrm;

use Janisbiz\LightOrm\Connection\ConnectionConfigInterface;
use Janisbiz\LightOrm\Connection\ConnectionInterface;

class ConnectionPool
{
    /**
     * @var ConnectionConfigInterface[]
     */
    protected static $connectionConfig = [];

    /**
     * @var ConnectionInterface[]
     */
    protected static $connections = [];

    /**
     * @param ConnectionConfigInterface $connectionConfig
     *
     * @return $this
     */
    public function addConnectionConfig(ConnectionConfigInterface $connectionConfig)
    {
        self::$connectionConfig[$connectionConfig->getDbname()] = $connectionConfig;

        return $this;
    }

    /**
     * @param string $dbName
     *
     * @return null|ConnectionInterface
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
     * @return ConnectionInterface
     */
    public static function getConnectionStatic($dbName)
    {
        return (new self())->getConnection($dbName);
    }

    /**
     * @param ConnectionConfigInterface $connectionConfig
     *
     * @return ConnectionInterface
     */
    protected function createConnection(ConnectionConfigInterface $connectionConfig)
    {
        $adapterConnectionClass = $connectionConfig->getAdapterConnectionClass();

        return new $adapterConnectionClass(
            $connectionConfig->generateDsn(),
            $connectionConfig->getUsername(),
            $connectionConfig->getPassword()
        );
    }
}
