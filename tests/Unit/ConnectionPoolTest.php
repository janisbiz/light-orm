<?php

namespace Janisbiz\LightOrm\Tests\Unit;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\ConnectionPool;
use Janisbiz\LightOrm\Dms\MySQL\Connection\ConnectionConfig;
use PHPUnit\Framework\TestCase;

class ConnectionPoolTest extends TestCase
{
    const CONNECTION_CONFIG_HOST = 'host';
    const CONNECTION_CONFIG_USERNAME = 'username';
    const CONNECTION_CONFIG_PASSWORD = 'password';
    const CONNECTION_CONFIG_DBNAME_ONE = 'dbname_one';
    const CONNECTION_CONFIG_DBNAME_TWO = 'dbname_two';
    const CONNECTION_CONFIG_DBNAME_NON_EXISTENT = 'dbname_non_existent';
    const CONNECTION_CONFIG_ADAPTER = ConnectionConfig::ADAPTER;

    /**
     * @var ConnectionPool
     */
    private $connectionPool;

    /**
     * @var ConnectionPool
     */
    private $connectionPoolMock;

    public function setUp()
    {
        $connectionConfigOne = new ConnectionConfig(
            self::CONNECTION_CONFIG_HOST,
            self::CONNECTION_CONFIG_USERNAME,
            self::CONNECTION_CONFIG_PASSWORD,
            self::CONNECTION_CONFIG_DBNAME_ONE
        );

        $connectionConfigTwo = new ConnectionConfig(
            self::CONNECTION_CONFIG_HOST,
            self::CONNECTION_CONFIG_USERNAME,
            self::CONNECTION_CONFIG_PASSWORD,
            self::CONNECTION_CONFIG_DBNAME_TWO
        );

        $this->connectionPool = (new ConnectionPool())
            ->addConnectionConfig($connectionConfigOne)
            ->addConnectionConfig($connectionConfigTwo)
        ;

        $connectionMock = $this->createMock(ConnectionInterface::class);

        $connectionPoolMock = $this->createPartialMock(ConnectionPool::class, ['createConnection']);
        $connectionPoolMock->method('createConnection')->willReturn($connectionMock);

        $this->connectionPoolMock = $connectionPoolMock;
    }

    public function testAddConnectionConfig()
    {
        $connectionPoolReflection = new \ReflectionClass($this->connectionPool);
        $connectionConfigProperty = $connectionPoolReflection->getProperty('connectionConfig');
        $connectionConfigProperty->setAccessible(true);
        $connectionConfig = $connectionConfigProperty->getValue($this->connectionPool);
        $connectionConfigProperty->setAccessible(false);

        $this->assertCount(2, $connectionConfig);
    }

    public function testGetConnection()
    {
        $this->assertTrue(
            $this->connectionPoolMock->getConnection(self::CONNECTION_CONFIG_DBNAME_ONE)
            instanceof ConnectionInterface
        );
        $this->assertTrue(
            $this->connectionPoolMock->getConnection(self::CONNECTION_CONFIG_DBNAME_TWO)
            instanceof ConnectionInterface
        );
    }

    /**
     * @codeCoverageIgnore
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage
     * Could not find connection by name "dbname_non_existent"! Available connections: "dbname_one", "dbname_two".
     */
    public function testGetConnectionException()
    {
        $this->connectionPoolMock->getConnection(self::CONNECTION_CONFIG_DBNAME_NON_EXISTENT);
    }

    public function testGetConnectionStatic()
    {
        $this->assertTrue(
            ConnectionPool::getConnectionStatic(self::CONNECTION_CONFIG_DBNAME_ONE)
            instanceof ConnectionInterface
        );
        $this->assertTrue(
            ConnectionPool::getConnectionStatic(self::CONNECTION_CONFIG_DBNAME_TWO)
            instanceof ConnectionInterface
        );
    }

    /**
     * @codeCoverageIgnore
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage
     * Could not find connection by name "dbname_non_existent"! Available connections: "dbname_one", "dbname_two".
     */
    public function testGetConnectionStaticException()
    {
        ConnectionPool::getConnectionStatic(self::CONNECTION_CONFIG_DBNAME_NON_EXISTENT);
    }
}
