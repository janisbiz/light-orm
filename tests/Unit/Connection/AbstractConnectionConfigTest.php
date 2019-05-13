<?php

namespace Janisbiz\LightOrm\Tests\Unit\Connection;

use Janisbiz\LightOrm\Connection\AbstractConnectionConfig;
use PHPUnit\Framework\TestCase;

class AbstractConnectionConfigTest extends TestCase
{
    const CONFIG_HOST = 'host';
    const CONFIG_USERNAME = 'username';
    const CONFIG_PASSWORD = 'password';
    const CONFIG_DBNAME = 'dbname';
    const CONFIG_ADAPTER = 'adapter';

    /**
     * @var AbstractConnectionConfig
     */
    private $abstractConnectionConfig;

    public function setUp()
    {
        $this->abstractConnectionConfig = $this->getMockForAbstractClass(
            AbstractConnectionConfig::class,
            [
                self::CONFIG_HOST,
                self::CONFIG_USERNAME,
                self::CONFIG_PASSWORD,
                self::CONFIG_DBNAME,
                self::CONFIG_ADAPTER,
            ]
        );
    }

    public function testGetUsername()
    {
        $this->assertEquals(self::CONFIG_USERNAME, $this->abstractConnectionConfig->getUsername());
    }

    public function testGetPassword()
    {
        $this->assertEquals(self::CONFIG_PASSWORD, $this->abstractConnectionConfig->getPassword());
    }

    public function testGetDbname()
    {
        $this->assertEquals(self::CONFIG_DBNAME, $this->abstractConnectionConfig->getDbname());
    }
}
