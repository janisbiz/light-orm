<?php declare(strict_types=1);

namespace Janisbiz\LightOrm;

use Janisbiz\LightOrm\Connection\ConnectionConfigInterface;
use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Connection\ConnectionInvalidArgumentException;

class ConnectionPool
{
    /**
     * @var ConnectionConfigInterface[]
     */
    protected static array $connectionConfig = [];

    /**
     * @var ConnectionInterface[]
     */
    protected static array $connections = [];

    public function addConnectionConfig(ConnectionConfigInterface $connectionConfig): static
    {
        static::$connectionConfig[$connectionConfig->getDbname()] = $connectionConfig;

        return $this;
    }

    /**
     * @throws ConnectionInvalidArgumentException
     */
    public function getConnection(string $dbName): ConnectionInterface
    {
        if (!array_key_exists($dbName, static::$connectionConfig)) {
            if (empty(static::$connectionConfig)) {
                throw new ConnectionInvalidArgumentException(sprintf(
                    'Could not find connection by name "%s"!',
                    $dbName
                ));
            } else {
                throw new ConnectionInvalidArgumentException(sprintf(
                    'Could not find connection by name "%s"! Available connections: "%s".',
                    $dbName,
                    implode('", "', array_keys(static::$connectionConfig))
                ));
            }
        }

        $connectionConfig = static::$connectionConfig[$dbName];

        static::$connections[$dbName] ??= $this->createConnection($connectionConfig);

        return static::$connections[$dbName];
    }

    public static function getConnectionStatic($dbName): ConnectionInterface
    {
        return (new self())->getConnection($dbName);
    }

    /**
     * Ignoring for code coverage, as this requires to have a real connection to be tested.
     * @codeCoverageIgnore
     */
    protected function createConnection(ConnectionConfigInterface $connectionConfig): ConnectionInterface
    {
        $adapterConnectionClass = $connectionConfig->getAdapterConnectionClass();

        return new $adapterConnectionClass(
            $connectionConfig->generateDsn(),
            $connectionConfig->getUsername(),
            $connectionConfig->getPassword()
        );
    }
}
