<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\Connection;

use Janisbiz\LightOrm\Dms\MySQL\Connection\Connection;
use PHPUnit\Framework\TestCase;

class ConnectionTest extends TestCase
{
    /**
     * @var Connection
     */
    private $connection;

    public function setUp()
    {
        $this->connection = $this->createPartialMock(
            Connection::class,
            [
                'inTransaction',
                'parentBeginTransaction'
            ]
        );
    }

    public function testBeginTransaction()
    {
        $this->connection->method('inTransaction')->willReturn(false);
        $this->connection->method('parentBeginTransaction')->willReturn(true);
        $this->assertTrue($this->connection->beginTransaction() instanceof Connection);
    }

    public function testBeginTransactionWhenInTransaction()
    {
        $this->connection->method('inTransaction')->willReturn(true);
        $this->assertTrue($this->connection->beginTransaction() instanceof Connection);
    }

    /**
     * @expectedException \PDOException
     * @expectedExceptionMessage Cannot begin transaction!
     */
    public function testBeginTransactionWhenNotInTransactionAndCantBeginTransaction()
    {
        $this->connection->method('inTransaction')->willReturn(false);
        $this->connection->method('parentBeginTransaction')->willReturn(false);
        $this->assertTrue($this->connection->beginTransaction() instanceof Connection);
    }
}
