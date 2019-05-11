<?php

namespace Janisbiz\LightOrm\Tests\Behat\Features\Dms\MySQL\Repository;

use Behat\Gherkin\Node\TableNode;
use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Dms\MySQL\Connection\Connection as MySQLConnection;
use Janisbiz\LightOrm\Repository\RepositoryInterface;

class RepositoryFeatureContext extends AbstractRepositoryFeatureContext
{
    /**
     * @Given /^I flush all tables for connection "(.*)"$/
     *
     * @param string $connectionName
     *
     * @throws \Exception
     */
    public function iFlushAllTablesForConnection($connectionName)
    {
        $connection = $this->connectionPool->getConnection($connectionName);

        $tables = [];
        switch (\get_class($connection)) {
            case MySQLConnection::class:
                $dmsDatabase = (new DmsFactory())->createDmsDatabase($connectionName, $connection);
                foreach ($dmsDatabase->getDmsTables() as $dmsTable) {
                    $tables[] = $dmsTable->getName();
                }

                break;

            default:
                throw new \Exception(\sprintf('Flush all tables for connection "%s" is no defined!', $connectionName));
        }

        $this->flushTables($connection, $connectionName, $tables);
    }

    /**
     * @Given /^I flush following tables for connection "(.*)":$/
     *
     * @param string $connectionName
     * @param TableNode $tables
     */
    public function iFlushFollowingTablesForConnection($connectionName, TableNode $tables)
    {
        $tablesArray = [];
        foreach ($tables->getTable() as $table) {
            $tablesArray[] = $table[0];
        }

        $this->flushTables($this->connectionPool->getConnection($connectionName), $connectionName, $tablesArray);
    }

    /**
     * @Given /^I reset database for connection "(.*)"$/
     *
     * @param string $connectionName
     */
    public function iResetDatabaseForConnection($connectionName)
    {
        $connection = $this->connectionPool->getConnection($connectionName);
        switch (\get_class($connection)) {
            case MySQLConnection::class:
                $connection->exec(\file_get_contents(\implode(
                    '',
                    [
                        JANISBIZ_LIGHT_ORM_ROOT_DIR,
                        '.docker',
                        DIRECTORY_SEPARATOR,
                        'config',
                        DIRECTORY_SEPARATOR,
                        'mysql',
                        DIRECTORY_SEPARATOR,
                        'light_orm_mysql.sql',
                    ]
                )));

                break;
        }
    }
    
    /**
     * @Given /^I create repository "(.*)"$/
     *
     * @param $repositoryClass
     *
     * @throws \Exception
     */
    public function iCreateRepository($repositoryClass)
    {
        static::$repository = new $repositoryClass;

        if (!static::$repository instanceof RepositoryInterface) {
            throw new \Exception(\sprintf(
                'Class "%s" must implement "%s"',
                \get_class(static::$repository),
                RepositoryInterface::class
            ));
        }
    }

    /**
     * @Then /^I have exception with message "(.*)"$/
     *
     * @param string $message
     *
     * @throws \Exception
     */
    public function iHaveExceptionWithMessage($message)
    {
        if (null === static::$exception) {
            throw new \Exception('There is no expected exception!');
        }

        if ($message !== static::$exception->getMessage()) {
            throw new \Exception(\sprintf(
                'Expected exception doesn\'t containt message "%s", it contains message "%s"!',
                $message,
                static::$exception->getMessage()
            ));
        }
    }

    /**
     * @Given /^I begin transaction on connection "(.*)"$/
     *
     * @param string $connectionName
     */
    public function iBeginTransactionOnConnection($connectionName)
    {
        $this->connectionPool->getConnection($connectionName)->beginTransaction();
    }

    /**
     * @Given /^I commit transaction on connection "(.*)"$/
     *
     * @param string $connectionName
     */
    public function iCommitTransactionOnConnection($connectionName)
    {
        $this->connectionPool->getConnection($connectionName)->commit();
    }

    /**
     * @param ConnectionInterface $connection
     * @param string $connectionName
     * @param array $tables
     *
     * @throws \Exception
     */
    private function flushTables(ConnectionInterface $connection, $connectionName, array $tables)
    {
        switch (\get_class($connection)) {
            case MySQLConnection::class:
                $connection->query('SET SESSION FOREIGN_KEY_CHECKS = 0');
                foreach ($tables as $table) {
                    $connection->query(\sprintf('TRUNCATE TABLE %s', $table));
                }
                $connection->query('SET SESSION FOREIGN_KEY_CHECKS = 1');

                break;

            default:
                throw new \Exception(\sprintf('Flush all tables for connection "%s" is no defined!', $connectionName));
        }
    }
}
