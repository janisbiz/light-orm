<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Connection;

use Exception;
use Janisbiz\LightOrm\Connection\AbstractConnectionConfig;
use PHPUnit\Framework\TestCase;

class AbstractConnectionConfigTest extends TestCase
{
    const string CONFIG_HOST = 'host';
    const string CONFIG_USERNAME = 'username';
    const string CONFIG_PASSWORD = 'password';
    const string CONFIG_DBNAME = 'dbname';
    const string CONFIG_ADAPTER = 'adapter';
    const int CONFIG_PORT = 3306;

    private AbstractConnectionConfig $abstractConnectionConfig;

    public function setUp(): void
    {
        $this->abstractConnectionConfig = new class(
            static::CONFIG_HOST,
            static::CONFIG_USERNAME,
            static::CONFIG_PASSWORD,
            static::CONFIG_DBNAME,
            static::CONFIG_ADAPTER,
            static::CONFIG_PORT,
        ) extends AbstractConnectionConfig {
            public function generateDsn(): never
            {
                throw new Exception('Not implemented');
            }

            public function getAdapterConnectionClass(): never
            {
                throw new Exception('Not implemented');
            }
        };
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
