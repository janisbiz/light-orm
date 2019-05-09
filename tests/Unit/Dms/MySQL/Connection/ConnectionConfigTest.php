<?php

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

    /**
     * @var ConnectionConfig
     */
    private $connectionConfig;

    public function setUp()
    {
        $this->connectionConfig = new ConnectionConfig(
            self::CONFIG_HOST,
            self::CONFIG_USERNAME,
            self::CONFIG_PASSWORD,
            self::CONFIG_DBNAME
        );
    }

    public function testGenerateDsn()
    {
        $this->assertEquals(
            \sprintf(
                '%s:host=%s;dbname=%s;charset=utf8mb4',
                self::CONFIG_ADAPTER,
                self::CONFIG_HOST,
                self::CONFIG_DBNAME
            ),
            $this->connectionConfig->generateDsn()
        );
    }

    public function testGetUsername()
    {
        $this->assertEquals(self::CONFIG_USERNAME, $this->connectionConfig->getUsername());
    }

    public function testGetPassword()
    {
        $this->assertEquals(self::CONFIG_PASSWORD, $this->connectionConfig->getPassword());
    }

    public function testGetDbname()
    {
        $this->assertEquals(self::CONFIG_DBNAME, $this->connectionConfig->getDbname());
    }

    public function testGetAdapterConnectionClass()
    {
        $this->assertEquals(Connection::class, $this->connectionConfig->getAdapterConnectionClass());
    }
}
