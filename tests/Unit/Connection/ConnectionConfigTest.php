<?php

namespace Janisbiz\LightOrm\Tests\Unit\Connection;

use Janisbiz\LightOrm\Connection\ConnectionConfig;
use Janisbiz\LightOrm\Dms\MySQL\Connection\Connection;
use PHPUnit\Framework\TestCase;

class ConnectionConfigTest extends TestCase
{
    const CONFIG_HOST = 'host';
    const CONFIG_USERNAME = 'username';
    const CONFIG_PASSWORD = 'password';
    const CONFIG_DBNAME = 'dbname';
    const CONFIG_ADAPTER = ConnectionConfig::ADAPTER_MYSQL;
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
            self::CONFIG_DBNAME,
            self::CONFIG_ADAPTER
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

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid adapter "adapter_invalid"! Supported adapters: "mysql"
     */
    public function testCreateConnectionConfigWhenInvalidAdapterProvided()
    {
        new ConnectionConfig(
            self::CONFIG_HOST,
            self::CONFIG_USERNAME,
            self::CONFIG_PASSWORD,
            self::CONFIG_DBNAME,
            self::CONFIG_ADAPTER_INVALID
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
