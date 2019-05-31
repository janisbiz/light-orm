<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\Connection;

class Connection extends \PDO implements ConnectionInterface
{
    /**
     * Ignoring for code coverage, as this requires to have a real connection to be tested.
     * @codeCoverageIgnore
     *
     * @param string $dsn
     * @param string $username
     * @param string $passwd
     * @param array $options
     */
    public function __construct(string $dsn, string $username, string $passwd, array $options = [])
    {
        $options = \array_merge(
            $options,
            [
                static::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES "utf8mb4"',
                static::ATTR_PERSISTENT => true,
            ]
        );

        parent::__construct($dsn, $username, $passwd, $options);

        $this->setAttribute(static::ATTR_ERRMODE, static::ERRMODE_EXCEPTION);
        $this->setAttribute(static::ATTR_EMULATE_PREPARES, false);
    }

    /**
     * @return $this
     * @throws ConnectionPDOException
     */
    public function beginTransaction(): Connection
    {
        if (false === $this->inTransaction()) {
            if (false === $this->parentBeginTransaction()) {
                throw new ConnectionPDOException('Cannot begin transaction!');
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function setSqlSafeUpdates(): Connection
    {
        $this->exec('SET SESSION SQL_SAFE_UPDATES = 1;');

        return $this;
    }

    /**
     * @return $this
     */
    public function unsetSqlSafeUpdates(): Connection
    {
        $this->exec('SET SESSION SQL_SAFE_UPDATES = 0;');

        return $this;
    }

    /**
     * Ignoring for code coverage, as this requires to have a real connection to be tested and it is not possible
     * to mock parent method call.
     * @codeCoverageIgnore
     *
     * @return bool
     */
    protected function parentBeginTransaction(): bool
    {
        return parent::beginTransaction();
    }
}
