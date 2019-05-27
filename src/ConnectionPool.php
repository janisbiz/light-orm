<?php

namespace Janisbiz\LightOrm;

use Janisbiz\LightOrm\Connection\ConnectionConfigInterface;
use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Connection\ConnectionInvalidArgumentException;

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
        static::$connectionConfig[$connectionConfig->getDbname()] = $connectionConfig;

        return $this;
    }

    /**
     * @param string $dbName
     *
     * @return null|ConnectionInterface
     */
    public function getConnection($dbName)
    {
        if (empty(static::$connectionConfig[$dbName])) {
            throw new ConnectionInvalidArgumentException(
                empty(static::$connectionConfig)
                    ? \sprintf('Could not find connection by name "%s"!', $dbName)
                    : \sprintf(
                        'Could not find connection by name "%s"! Available connections: "%s".',
                        $dbName,
                        \implode('", "', \array_keys(static::$connectionConfig))
                    )
            );
        }

        $connectionConfig = static::$connectionConfig[$dbName];

        if (!isset(static::$connections[$dbName])) {
            static::$connections[$dbName] = $this->createConnection($connectionConfig);
        }

        return static::$connections[$dbName];
    }

    /**
     * @param string $dbName
     *
     * @return ConnectionInterface
     */
    public static function getConnectionStatic($dbName)
    {
        return (new static())->getConnection($dbName);
    }

    /**
     * Ignoring for code coverage, as this requires to have a real connection to be tested.
     * @codeCoverageIgnore
     *
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
