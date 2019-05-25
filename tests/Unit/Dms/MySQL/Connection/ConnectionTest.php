<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\Connection;

use Janisbiz\LightOrm\Dms\MySQL\Connection\Connection;
use PHPUnit\Framework\TestCase;

class ConnectionTest extends TestCase
{
    /**
     * @var Connection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $connection;

    public function setUp()
    {
        $this->connection = $this->createPartialMock(
            Connection::class,
            [
                'inTransaction',
                'parentBeginTransaction',
                'exec',
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

    public function testSetSqlSafeUpdates()
    {
        $this
            ->connection
            ->expects($this->once())
            ->method('exec')
            ->withConsecutive([
                'SET SESSION SQL_SAFE_UPDATES = 1;',
            ])
        ;
        $this->assertTrue($this->connection->setSqlSafeUpdates() instanceof Connection);
    }

    public function testUnsetSqlSafeUpdates()
    {
        $this
            ->connection
            ->expects($this->once())
            ->method('exec')
            ->withConsecutive([
                'SET SESSION SQL_SAFE_UPDATES = 0;',
            ])
        ;
        $this->assertTrue($this->connection->unsetSqlSafeUpdates() instanceof Connection);
    }

    /**
     * @codeCoverageIgnore
     * @expectedException \Janisbiz\LightOrm\Dms\MySQL\Connection\ConnectionPDOException
     * @expectedExceptionMessage Cannot begin transaction!
     */
    public function testBeginTransactionWhenNotInTransactionAndCantBeginTransaction()
    {
        $this->connection->method('inTransaction')->willReturn(false);
        $this->connection->method('parentBeginTransaction')->willReturn(false);
        $this->assertTrue($this->connection->beginTransaction() instanceof Connection);
    }
}
