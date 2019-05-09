<?php

namespace Janisbiz\LightOrm\Tests\Behat\Features\Connection;

use Janisbiz\LightOrm\Connection\ConnectionConfigInterface;
use Janisbiz\LightOrm\Dms\MySQL\Connection\ConnectionConfig as MySQLConnectionConfig;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\FeatureContext;

class ConnectionFeatureContext extends FeatureContext
{
    /**
     * @var ConnectionConfigInterface
     */
    private $connectionConfig;

    /**
     * @Given /^I have existing connection config "(\w+)"$/
     *
     * @param string $connectionName
     *
     * @throws \Exception
     */
    public function iHaveExistingConnectionConfig($connectionName)
    {
        $connectionConfigArray = $this->getConnectionConfig($connectionName);

        switch ($connectionConfigArray['adapter']) {
            case MySQLConnectionConfig::ADAPTER:
                $this->connectionConfig = new MySQLConnectionConfig(
                    $connectionConfigArray['host'],
                    $connectionConfigArray['username'],
                    $connectionConfigArray['password'],
                    $connectionConfigArray['dbname']
                );

                break;
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
     * @Then /^I should have connection "(\w+)"$/
     *
     * @param string $connectionName
     */
    public function iShouldHaveConnection($connectionName)
    {
        $this->connectionPool->getConnection($connectionName);
    }
}
