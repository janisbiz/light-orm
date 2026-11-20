<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\Connection;

use Janisbiz\LightOrm\Dms\MySQL\Connection\Connection;
use Janisbiz\LightOrm\Dms\MySQL\Connection\ConnectionPDOException;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class ConnectionTest extends TestCase
{
    private Connection|MockObject $connection;

    public function setUp(): void
    {
        $this->connection = $this
            ->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'exec',
                'inTransaction',
                'parentBeginTransaction',
            ])
            ->getMock()
        ;
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
            ->with('SET SESSION SQL_SAFE_UPDATES = 1;')
        ;
        $this->assertTrue($this->connection->setSqlSafeUpdates() instanceof Connection);
    }

    public function testUnsetSqlSafeUpdates()
    {
        $this
            ->connection
            ->expects($this->once())
            ->method('exec')
            ->with('SET SESSION SQL_SAFE_UPDATES = 0;')
        ;
        $this->assertTrue($this->connection->unsetSqlSafeUpdates() instanceof Connection);
    }

    public function testBeginTransactionWhenNotInTransactionAndCantBeginTransaction()
    {
        $this->connection->method('inTransaction')->willReturn(false);
        $this->connection->method('parentBeginTransaction')->willReturn(false);

        $this->expectException(ConnectionPDOException::class);
        $this->expectExceptionMessage('Cannot begin transaction!');

        $this->connection->beginTransaction();
    }
}
