<?php declare(strict_types=1);

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
                static::CONFIG_HOST,
                static::CONFIG_USERNAME,
                static::CONFIG_PASSWORD,
                static::CONFIG_DBNAME,
                static::CONFIG_ADAPTER,
            ]
        );
    }

    public function testGetUsername()
    {
        $this->assertEquals(static::CONFIG_USERNAME, $this->abstractConnectionConfig->getUsername());
    }

    public function testGetPassword()
    {
        $this->assertEquals(static::CONFIG_PASSWORD, $this->abstractConnectionConfig->getPassword());
    }

    public function testGetDbname()
    {
        $this->assertEquals(static::CONFIG_DBNAME, $this->abstractConnectionConfig->getDbname());
    }
}
