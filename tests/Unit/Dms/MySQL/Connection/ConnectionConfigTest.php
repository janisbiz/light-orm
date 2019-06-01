<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\Connection;

use Janisbiz\LightOrm\Dms\MySQL\Connection\Connection;
use Janisbiz\LightOrm\Dms\MySQL\Connection\ConnectionConfig;
use PHPUnit\Framework\TestCase;

class ConnectionConfigTest extends TestCase
{
    const CONFIG_HOST = 'host';
    const CONFIG_USERNAME = 'username';
    const CONFIG_PASSWORD = 'password';
    const CONFIG_DBNAME = 'dbname';
    const CONFIG_ADAPTER = ConnectionConfig::ADAPTER;
    const CONFIG_ADAPTER_INVALID = 'adapter_invalid';
    const CONFIG_PORT = 1234;

    /**
     * @var ConnectionConfig
     */
    private $connectionConfig;

    public function setUp()
    {
        $this->connectionConfig = new ConnectionConfig(
            static::CONFIG_HOST,
            static::CONFIG_USERNAME,
            static::CONFIG_PASSWORD,
            static::CONFIG_DBNAME,
            static::CONFIG_PORT
        );
    }

    public function testGenerateDsn()
    {
        $this->assertEquals(
            \sprintf(
                '%s:host=%s;dbname=%s;charset=utf8mb4;port=%d',
                static::CONFIG_ADAPTER,
                static::CONFIG_HOST,
                static::CONFIG_DBNAME,
                static::CONFIG_PORT
            ),
            $this->connectionConfig->generateDsn()
        );
    }

    public function testGetUsername()
    {
        $this->assertEquals(static::CONFIG_USERNAME, $this->connectionConfig->getUsername());
    }

    public function testGetPassword()
    {
        $this->assertEquals(static::CONFIG_PASSWORD, $this->connectionConfig->getPassword());
    }

    public function testGetDbname()
    {
        $this->assertEquals(static::CONFIG_DBNAME, $this->connectionConfig->getDbname());
    }

    public function testGetAdapterConnectionClass()
    {
        $this->assertEquals(Connection::class, $this->connectionConfig->getAdapterConnectionClass());
    }
}
