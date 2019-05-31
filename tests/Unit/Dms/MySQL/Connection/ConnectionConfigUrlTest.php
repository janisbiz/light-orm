<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\Connection;

use Janisbiz\LightOrm\Dms\MySQL\Connection\ConnectionConfigUrl;
use PHPUnit\Framework\TestCase;

class ConnectionConfigUrlTest extends TestCase
{
    const URL_PART_SCHEME = 'mysql';
    const URL_PART_USER = 'user';
    const URL_PART_PASS = 'password';
    const URL_PART_HOST = 'host';
    const URL_PART_PORT = 1234;
    const URL_PART_PATH = 'database_name';

    public function testConstruct()
    {
        $connectionConfigUrl = new ConnectionConfigUrl(
            \sprintf(
                '%s://%s:%s@%s:%d/%s',
                static::URL_PART_SCHEME,
                static::URL_PART_USER,
                static::URL_PART_PASS,
                static::URL_PART_HOST,
                static::URL_PART_PORT,
                static::URL_PART_PATH
            )
        );

        $this->assertEquals(
            \sprintf(
                '%s:host=%s;dbname=%s;charset=utf8mb4;port=%d',
                static::URL_PART_SCHEME,
                static::URL_PART_HOST,
                static::URL_PART_PATH,
                static::URL_PART_PORT
            ),
            $connectionConfigUrl->generateDsn()
        );
        $this->assertEquals(static::URL_PART_USER, $connectionConfigUrl->getUsername());
        $this->assertEquals(static::URL_PART_PASS, $connectionConfigUrl->getPassword());
        $this->assertEquals(static::URL_PART_PATH, $connectionConfigUrl->getDbname());
    }
}
