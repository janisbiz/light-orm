<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\ConnectionPool;
use Janisbiz\LightOrm\Dms\MySQL\Connection\ConnectionConfig;
use Janisbiz\LightOrm\Exception\InvalidArgumentException;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class ConnectionPoolTest extends TestCase
{
    use ReflectionTrait;

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

    public function setUp(): void
    {
        $connectionConfigOne = new ConnectionConfig(
            static::CONNECTION_CONFIG_HOST,
            static::CONNECTION_CONFIG_USERNAME,
            static::CONNECTION_CONFIG_PASSWORD,
            static::CONNECTION_CONFIG_DBNAME_ONE
        );

        $connectionConfigTwo = new ConnectionConfig(
            static::CONNECTION_CONFIG_HOST,
            static::CONNECTION_CONFIG_USERNAME,
            static::CONNECTION_CONFIG_PASSWORD,
            static::CONNECTION_CONFIG_DBNAME_TWO
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
        $connectionConfig = $connectionConfigProperty->getValue($this->connectionPool);

        $this->assertCount(2, $connectionConfig);
    }

    public function testGetConnection()
    {
        $this->assertTrue(
            $this->connectionPoolMock->getConnection(static::CONNECTION_CONFIG_DBNAME_ONE)
            instanceof ConnectionInterface
        );
        $this->assertTrue(
            $this->connectionPoolMock->getConnection(static::CONNECTION_CONFIG_DBNAME_TWO)
            instanceof ConnectionInterface
        );
    }

    public function testGetConnectionExceptionWhenConnectionConfigIsEmpty()
    {
        $this
            ->createAccessibleProperty($this->connectionPoolMock, 'connectionConfig')
            ->setValue($this->connectionPoolMock, [])
        ;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches(
            '/Could not find connection by name "(.*)"\!/'
        );

        $this->connectionPoolMock->getConnection(static::CONNECTION_CONFIG_DBNAME_NON_EXISTENT);
    }

    public function testGetConnectionException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches(
            '/Could not find connection by name "(.*)"\! Available connections: "(.*)"\./'
        );

        $this->connectionPoolMock->getConnection(static::CONNECTION_CONFIG_DBNAME_NON_EXISTENT);
    }

    public function testGetConnectionStatic()
    {
        $this->assertTrue(
            ConnectionPool::getConnectionStatic(static::CONNECTION_CONFIG_DBNAME_ONE)
            instanceof ConnectionInterface
        );
        $this->assertTrue(
            ConnectionPool::getConnectionStatic(static::CONNECTION_CONFIG_DBNAME_TWO)
            instanceof ConnectionInterface
        );
    }

    public function testGetConnectionStaticException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches(
            '/Could not find connection by name "(.*)"\! Available connections: "(.*)"\./'
        );

        ConnectionPool::getConnectionStatic(static::CONNECTION_CONFIG_DBNAME_NON_EXISTENT);
    }
}
