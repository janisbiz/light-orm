<?php

namespace Janisbiz\LightOrm\Tests\Unit\Repository;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Repository\AbstractRepository;
use PHPUnit\Framework\TestCase;

class AbstractRepositoryTest extends TestCase
{
    const VALUE = 'value';
    const VALUE_INVALID = [];

    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @var AbstractRepository
     */
    private $abstractRepository;

    public function setUp()
    {
        $this->connection = $this
            ->createPartialMock(
                ConnectionInterface::class,
                [
                    'beginTransaction',
                    'quote',
                ]
            )
        ;

        $this->abstractRepository = $this
            ->getMockForAbstractClass(
                AbstractRepository::class,
                [],
                '',
                true,
                true,
                true,
                [
                    'getConnection',
                ]
            );
        $this->abstractRepository
            ->expects($this->any())
            ->method('getConnection')
            ->willReturn($this->connection)
        ;
    }

    public function testQuote()
    {
        $this->connection->expects($this->once())->method('quote');
        $this->abstractRepository->quote(self::VALUE);
    }

    /**
     * @codeCoverageIgnore
     * @expectedException \Exception
     * @expectedExceptionMessage Parameter type "array" could not be quoted for SQL execution!
     */
    public function testQuoteWithInvalidValue()
    {
        $this->abstractRepository->quote(self::VALUE_INVALID);
    }
}
