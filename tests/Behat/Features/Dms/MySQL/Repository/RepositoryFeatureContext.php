<?php

namespace Janisbiz\LightOrm\Tests\Behat\Features\Dms\MySQL\Repository;

use Behat\Gherkin\Node\TableNode;
use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Dms\MySQL\Connection\Connection as MySQLConnection;
use Janisbiz\LightOrm\Dms\MySQL\Generator\DmsFactory;
use Janisbiz\LightOrm\Repository\RepositoryInterface;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\FeatureContext;

class RepositoryFeatureContext extends FeatureContext
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var \Exception
     */
    private $exception;

    /**
     * @Given /^I create repository "(.*)"$/
     *
     * @param $repositoryClass
     *
     * @throws \Exception
     */
    public function iCreateRepository($repositoryClass)
    {
        $this->repository = new $repositoryClass;

        if (!$this->repository instanceof RepositoryInterface) {
            throw new \Exception(\sprintf(
                'Class "%s" must implement "%s"',
                \get_class($this->repository),
                RepositoryInterface::class
            ));
        }
    }

    /**
     * @When /^I call method "(.*)" on repository with parameters:$/
     *
     * @param string $method
     * @param TableNode $parameters
     */
    public function iCallMethodOnRepositoryWithParameters($method, TableNode $parameters)
    {
        try {
            foreach ($parameters as $methodParameters) {
                \call_user_func_array([$this->repository, $method], $methodParameters);
            }
        } catch (\Exception $e) {
            $this->exception = $e;
        }
    }

    /**
     * @Then /^I call method "(.*)" on repository which will return following rows:$/
     *
     * @param string $method
     * @param TableNode $rows
     *
     * @throws \Exception
     */
    public function iCallMethodOnRepositoryWhichWillReturnFollowingRows($method, TableNode $rows)
    {
        $returnedRows = $this->repository->$method();

        if (\count($returnedRows) !== \count($rows->getRows()) - 1) {
            throw new \Exception('Count of expected rows doesn\'t match count of returned rows!');
        }

        foreach ($rows as $i => $row) {
            $returnedRow = $returnedRows[$i];

            foreach ($row as $column => $value) {
                $getterMethod = \sprintf('get%s', \ucfirst($column));
                if ($value != $returnedRow->$getterMethod()) {
                    throw new \Exception(\sprintf(
                        'Data mismatch, when reading stored row data! %s::%s => %s != %s => %s',
                        \get_class($returnedRow),
                        $getterMethod,
                        $returnedRow->$getterMethod(),
                        $column,
                        $value
                    ));
                }
            }
        }
    }

    /**
     * @Given /^I begin transaction on connection "(.*)"$/
     *
     * @param string $connectionName
     */
    public function iBeginTransactionOnRepository($connectionName)
    {
        $this->connectionPool->getConnection($connectionName)->beginTransaction();
    }

    /**
     * @Given /^I commit transaction on connection "(.*)"$/
     *
     * @param string $connectionName
     */
    public function iCommitTransactionOnRepository($connectionName)
    {
        $this->connectionPool->getConnection($connectionName)->commit();
    }

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

        $this->flushTables($connection, $tables);
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

        $this->flushTables($this->connectionPool->getConnection($connectionName), $tablesArray);
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
     * @Then /^I have exception with message "(.*)"$/
     *
     * @param string $message
     *
     * @throws \Exception
     */
    public function asd($message)
    {
        if (null === $this->exception) {
            throw new \Exception('There is no expected exception!');
        }

        if ($message !== $this->exception->getMessage()) {
            throw new \Exception(\sprintf(
                'Expected exception doesn\'t containt message "%s", it contains message "%s"!',
                $message,
                $this->exception->getMessage()
            ));
        }
    }

    /**
     * @param ConnectionInterface $connection
     * @param array $tables
     */
    private function flushTables(ConnectionInterface $connection, array $tables)
    {
        $connection->query('SET SESSION FOREIGN_KEY_CHECKS = 0');
        foreach ($tables as $table) {
            $connection->query(\sprintf('TRUNCATE TABLE %s', $table));
        }
        $connection->query('SET SESSION FOREIGN_KEY_CHECKS = 1');
    }
}
