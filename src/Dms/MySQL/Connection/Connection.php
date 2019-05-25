<?php

namespace Janisbiz\LightOrm\Dms\MySQL\Connection;

class Connection extends \PDO implements ConnectionInterface
{
    /**
     * @param string $dsn
     * @param string $username
     * @param string $passwd
     * @param array $options
     */
    public function __construct($dsn, $username, $passwd, array $options = [])
    {
        $options = \array_merge(
            $options,
            [
                self::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES "utf8mb4"',
                self::ATTR_PERSISTENT => true,
            ]
        );

        parent::__construct($dsn, $username, $passwd, $options);

        $this->setAttribute(self::ATTR_ERRMODE, self::ERRMODE_EXCEPTION);
        $this->setAttribute(self::ATTR_EMULATE_PREPARES, false);
    }

    /**
     * @return $this
     * @throws ConnectionPDOException
     */
    public function beginTransaction()
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
    public function setSqlSafeUpdates()
    {
        $this->exec('SET SESSION SQL_SAFE_UPDATES = 1;');

        return $this;
    }

    /**
     * @return $this
     */
    public function unsetSqlSafeUpdates()
    {
        $this->exec('SET SESSION SQL_SAFE_UPDATES = 0;');

        return $this;
    }

    /**
     * @return bool
     */
    protected function parentBeginTransaction()
    {
        return parent::beginTransaction();
    }
}
