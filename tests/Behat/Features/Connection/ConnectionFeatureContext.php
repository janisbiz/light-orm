<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Behat\Features\Connection;

use Janisbiz\LightOrm\Connection\ConnectionConfigInterface;
use Janisbiz\LightOrm\Dms\MySQL\Connection\ConnectionConfig as MySQLConnectionConfig;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\AbstractFeatureContext;

class ConnectionFeatureContext extends AbstractFeatureContext
{
    /**
     * @var ConnectionConfigInterface
     */
    private $connectionConfig;

    /**
     *
     * @param string $connectionName
     * @throws \Exception
     */
    #[\Behat\Step\Given('/^I have existing connection config "(\w+)"$/')]
    public function iHaveExistingConnectionConfig(string $connectionName)
    {
        $connectionConfigArray = $this->getConnectionConfig($connectionName);

        switch ($connectionConfigArray['adapter']) {
            case MySQLConnectionConfig::ADAPTER:
                $this->connectionConfig = new MySQLConnectionConfig(
                    $connectionConfigArray['host'],
                    $connectionConfigArray['username'],
                    $connectionConfigArray['password'],
                    $connectionConfigArray['dbname'],
                    $connectionConfigArray['port']
                );

                break;
        }
    }

    #[\Behat\Step\When('I add connection config to connection pool')]
    public function iAddConnectionConfigToConnectionPool()
    {
        $this->connectionPool->addConnectionConfig($this->connectionConfig);
    }

    /**
     * @param string $connectionName
     */
    #[\Behat\Step\Then('/^I should have connection "(\w+)"$/')]
    public function iShouldHaveConnection(string $connectionName)
    {
        $this->connectionPool->getConnection($connectionName);
    }
}
