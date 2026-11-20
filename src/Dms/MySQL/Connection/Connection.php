<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\Connection;

use PDO;
use ReturnTypeWillChange;

class Connection extends PDO implements ConnectionInterface
{
    private const DEFAULT_OPTIONS = [
        PDO\Mysql::ATTR_INIT_COMMAND => 'SET NAMES "utf8mb4"',
        PDO::ATTR_PERSISTENT => true,
    ];

    /**
     * Ignoring for code coverage, as this requires to have a real connection to be tested.
     * @codeCoverageIgnore
     */
    public function __construct(string $dsn, string $username, string $passwd, array $options = [])
    {
        $options = $options + self::DEFAULT_OPTIONS;

        parent::__construct($dsn, $username, $passwd, $options);

        $this->setAttribute(static::ATTR_ERRMODE, static::ERRMODE_EXCEPTION);
        $this->setAttribute(static::ATTR_EMULATE_PREPARES, false);
    }

    /**
     * @throws ConnectionPDOException
     */
    #[ReturnTypeWillChange]
    public function beginTransaction(): static
    {
        if (false === $this->inTransaction()) {
            if (false === $this->parentBeginTransaction()) {
                throw new ConnectionPDOException('Cannot begin transaction!');
            }
        }

        return $this;
    }

    public function setSqlSafeUpdates(): static
    {
        $this->exec('SET SESSION SQL_SAFE_UPDATES = 1;');

        return $this;
    }

    public function unsetSqlSafeUpdates(): static
    {
        $this->exec('SET SESSION SQL_SAFE_UPDATES = 0;');

        return $this;
    }

    /**
     * Ignoring for code coverage, as this requires to have a real connection to be tested and it is not possible
     * to mock parent method call.
     * @codeCoverageIgnore
     */
    protected function parentBeginTransaction(): bool
    {
        return parent::beginTransaction();
    }
}
