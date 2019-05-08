<?php

namespace Janisbiz\LightOrm\Tests\Behat\Features\Connection;

use Janisbiz\LightOrm\Connection\ConnectionConfig;
use Janisbiz\LightOrm\Connection\ConnectionConfigInterface;
use Janisbiz\LightOrm\ConnectionPool;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\FeatureContext;

class ConnectionFeatureContext extends FeatureContext
{
    /**
     * @var ConnectionConfigInterface
     */
    private $connectionConfig;

    /**
     * @var \Exception
     */
    private $exception;

    /**
     * @Given /^I have existing connection config "(\w+)"/
     *
     * @param string $connectionName
     *
     * @throws \Exception
     */
    public function iHaveExistingConnectionConfig($connectionName)
    {
        $connectionConfigArray = $this->getConnectionConfig($connectionName);

        $this->connectionConfig = new ConnectionConfig(
            $connectionConfigArray['host'],
            $connectionConfigArray['username'],
            $connectionConfigArray['password'],
            $connectionConfigArray['dbname'],
            $connectionConfigArray['adapter']
        );
    }

    /**
     * @Given /^I have non-existing connection config "(\w+)"/
     *
     * @param string $connectionName
     */
    public function iHaveNonExistingConnectionConfig($connectionName)
    {
        try {
            $this->getConnectionConfig($connectionName);

            throw new \Exception(\sprintf('Connection config with name "%s" exists!', $connectionName));
        } catch (\Exception $e) {
            $this->exception = $e;
        }
    }

    /**
     * @When I add connection config to connection pool
     */
    public function iAddConnectionConfigToConnectionPool()
    {
        $this->connectionPool->addConnectionConfig($this->connectionConfig);
    }

    /**
     * @Then /^I should have connection "(\w+)"/
     *
     * @param string $connectionName
     */
    public function iShouldHaveConnection($connectionName)
    {
        $this->connectionPool->getConnection($connectionName);
    }
}
