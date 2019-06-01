<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Behat\Features\Dms\MySQL\Repository;

use Behat\Gherkin\Node\TableNode;
use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Dms\MySQL\Connection\Connection as MySQLConnection;
use Janisbiz\LightOrm\Dms\MySQL\Generator\DmsFactory;
use Janisbiz\LightOrm\Repository\RepositoryInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerAwareInterface;

class RepositoryFeatureContext extends AbstractRepositoryFeatureContext
{
    /**
     * @BeforeScenario
     */
    public function beforeScenario()
    {
        static::$repositories = [];
    }

    /**
     * @Given /^I flush all tables for connection "(.*)"$/
     *
     * @param string $connectionName
     *
     * @throws \Exception
     */
    public function iFlushAllTablesForConnection(string $connectionName)
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
    public function iFlushFollowingTablesForConnection(string $connectionName, TableNode $tables)
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
    public function iResetDatabaseForConnection(string $connectionName)
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
    public function iCreateRepository(string $repositoryClass)
    {
        if (\array_key_exists($repositoryClass, static::$repositories)) {
            return;
        }

        $repository = new $repositoryClass;

        if (!$repository instanceof RepositoryInterface) {
            throw new \Exception(\sprintf(
                'Class "%s" must implement "%s"',
                \get_class($repository),
                RepositoryInterface::class
            ));
        }

        $logger = new Logger('light-orm-mysql');
        $logger->pushHandler(new StreamHandler(\implode(
            '',
            [
                JANISBIZ_LIGHT_ORM_ROOT_DIR,
                'var',
                DIRECTORY_SEPARATOR,
                'log',
                DIRECTORY_SEPARATOR,
                'light-orm-mysql.log'
            ]
        )));

        /** @var LoggerAwareInterface $repository */
        $repository->setLogger($logger);

        static::$repositories[$repositoryClass] = static::$repository = $repository;
    }

    /**
     * @Given /^I make repository "(.*)" as active repository$/
     *
     * @param string $repositoryClass
     *
     * @throws \Exception
     */
    public function iMakeRepositoryAsActiveRepository(string $repositoryClass)
    {
        if (!\array_key_exists($repositoryClass, static::$repositories)) {
            throw new \Exception(\sprintf(
                'There is no present repository "%s"! Please create repository before making it active!',
                $repositoryClass
            ));
        }

        static::$repository = static::$repositories[$repositoryClass];
    }

    /**
     * @Then /^I have exception with message "(.*)"$/
     *
     * @param string $message
     *
     * @throws \Exception
     */
    public function iHaveExceptionWithMessage(string $message)
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
    public function iBeginTransactionOnConnection(string $connectionName)
    {
        $this->connectionPool->getConnection($connectionName)->beginTransaction();
    }

    /**
     * @Given /^I commit transaction on connection "(.*)"$/
     *
     * @param string $connectionName
     */
    public function iCommitTransactionOnConnection(string $connectionName)
    {
        $this->connectionPool->getConnection($connectionName)->commit();
    }

    /**
     * @Given /^I rollback transaction on connection "(.*)"$/
     *
     * @param string $connectionName
     */
    public function iRollbackTransactionOnConnection(string $connectionName)
    {
        $this->connectionPool->getConnection($connectionName)->rollBack();
    }

    /**
     * @param ConnectionInterface $connection
     * @param string $connectionName
     * @param array $tables
     *
     * @throws \Exception
     */
    private function flushTables(ConnectionInterface $connection, string $connectionName, array $tables)
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
