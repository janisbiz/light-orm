<?php

namespace Janisbiz\LightOrm\Tests\Unit\Repository;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Connection\ConnectionInvalidArgumentException;
use Janisbiz\LightOrm\ConnectionPool;
use Janisbiz\LightOrm\QueryBuilder\QueryBuilderInterface;
use Janisbiz\LightOrm\Repository\AbstractRepository;
use Janisbiz\LightOrm\Repository\RepositoryException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;

class AbstractRepositoryTest extends TestCase
{
    const DATABASE_NAME = 'database_name';

    const CONSTANT = 'constant_value';
    const CONSTANT_NAME = 'CONSTANT';
    const CONSTANT_NAME_NON_EXISTENT = 'CONSTANT_NON_EXISTENT';

    const VALUE_INVALID = [];

    /**
     * @var ConnectionInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $connection;

    /**
     * @var \PDOStatement|\PHPUnit_Framework_MockObject_MockObject
     */
    private $pdoStatement;

    /**
     * @var QueryBuilderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queryBuilder;

    /**
     * @var AbstractRepository
     */
    private $abstractRepository;

    /**
     * @var \ReflectionMethod
     */
    private $abstractRepositoryBeginTransactionMethod;

    /**
     * @var \ReflectionMethod
     */
    private $abstractRepositoryCommitMethod;

    /**
     * @var \ReflectionMethod
     */
    private $abstractRepositoryRollBackMethod;

    /**
     * @var \ReflectionMethod
     */
    private $abstractRepositoryPrepareAndExecuteMethod;

    /**
     * @var \ReflectionMethod
     */
    private $abstractRepositoryGetConnectionMethod;

    /**
     * @var \ReflectionMethod
     */
    private $abstractRepositoryGetModelConstantMethod;

    /**
     * @var ConnectionPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $connectionPool;

    /**
     * @var \ReflectionProperty
     */
    private $abstractRepositoryConnectionPoolProperty;

    public function setUp()
    {
        $this->connection = $this
            ->createPartialMock(
                ConnectionInterface::class,
                [
                    'beginTransaction',
                    'inTransaction',
                    'quote',
                    'commit',
                    'rollBack',
                    'prepare',
                    'execute',
                ]
            )
        ;

        $this->pdoStatement = $this->createMock(\PDOStatement::class);

        $this->queryBuilder = $this->createMock(QueryBuilderInterface::class);

        $this->abstractRepository = $this->getMockForAbstractClass(
            AbstractRepository::class,
            [],
            '',
            true,
            true,
            true
            ,
            [
                'getModelClass',
            ]
        );
        $this->abstractRepository->method('getModelClass')->willReturn(static::class);

        $this->abstractRepositoryBeginTransactionMethod = new \ReflectionMethod(
            $this->abstractRepository,
            'beginTransaction'
        );
        $this->abstractRepositoryBeginTransactionMethod->setAccessible(true);

        $this->abstractRepositoryCommitMethod = new \ReflectionMethod(
            $this->abstractRepository,
            'commit'
        );
        $this->abstractRepositoryCommitMethod->setAccessible(true);

        $this->abstractRepositoryRollBackMethod = new \ReflectionMethod(
            $this->abstractRepository,
            'rollBack'
        );
        $this->abstractRepositoryRollBackMethod->setAccessible(true);

        $this->abstractRepositoryPrepareAndExecuteMethod = new \ReflectionMethod(
            $this->abstractRepository,
            'prepareAndExecute'
        );
        $this->abstractRepositoryPrepareAndExecuteMethod->setAccessible(true);

        $this->abstractRepositoryGetConnectionMethod = new \ReflectionMethod(
            $this->abstractRepository,
            'getConnection'
        );
        $this->abstractRepositoryGetConnectionMethod->setAccessible(true);

        $this->abstractRepositoryGetModelConstantMethod = new \ReflectionMethod(
            $this->abstractRepository,
            'getModelClassConstant'
        );
        $this->abstractRepositoryGetModelConstantMethod->setAccessible(true);

        $this->connectionPool = $this->createMock(ConnectionPool::class);
        $this->connectionPool->method('getConnection')->willReturn($this->connection);

        $this->abstractRepositoryConnectionPoolProperty = new \ReflectionProperty(
            $this->abstractRepository,
            'connectionPool'
        );
        $this->abstractRepositoryConnectionPoolProperty->setAccessible(true);
        $this->abstractRepositoryConnectionPoolProperty->setValue($this->abstractRepository, $this->connectionPool);
    }

    /**
     * @dataProvider quoteData
     *
     * @param null|int|string|double|bool $value
     */
    public function testQuote($value)
    {
        $this->connection->expects($this->once())->method('quote');
        $this->abstractRepository->quote($value);
    }

    /**
     * @return array
     */
    public function quoteData()
    {
        return [
            [
                null,
            ],
            [
                0,
            ],
            [
                'string',
            ],
            [
                0.0,
            ],
            [
                true,
            ],
        ];
    }

    public function testQuoteWithInvalidValue()
    {
        $this->expectException(RepositoryException::class);
        $this->expectExceptionMessage('Parameter type "array" could not be quoted for SQL execution!');

        $this->abstractRepository->quote(static::VALUE_INVALID);
    }

    public function testLog()
    {
        $this->abstractRepository->setLogger(new NullLogger());
        $abstractRepository = $this->abstractRepository->log(
            LogLevel::DEBUG,
            'Test Message',
            [
                'contextParam' => 'contextValue',
            ]
        );

        $this->assertTrue($abstractRepository instanceof $this->abstractRepository);
    }

    public function testLogWithoutLogger()
    {
        $abstractRepository = $this->abstractRepository->log(
            LogLevel::DEBUG,
            'Test Message',
            [
                'contextParam' => 'contextValue',
            ]
        );

        $this->assertTrue($abstractRepository instanceof $this->abstractRepository);
    }

    public function testBeginTransaction()
    {
        $this->connection->expects($this->once())->method('inTransaction')->willReturn(false);
        $this->connection->expects($this->once())->method('beginTransaction');

        $abstractRepository = $this
            ->abstractRepositoryBeginTransactionMethod
            ->invoke($this->abstractRepository, $this->connection)
        ;

        $this->assertTrue($abstractRepository instanceof $this->abstractRepository);
    }

    public function testBeginTransactionWhenInTransaction()
    {
        $this->connection->expects($this->once())->method('inTransaction')->willReturn(true);
        $this->connection->expects($this->never())->method('beginTransaction');

        $abstractRepository = $this
            ->abstractRepositoryBeginTransactionMethod
            ->invoke($this->abstractRepository, $this->connection)
        ;

        $this->assertTrue($abstractRepository instanceof $this->abstractRepository);
    }

    public function testBeginTransactionWithoutConnection()
    {
        $abstractRepository = $this
            ->abstractRepositoryBeginTransactionMethod
            ->invoke($this->abstractRepository)
        ;

        $this->assertTrue($abstractRepository instanceof $this->abstractRepository);
    }

    public function testCommit()
    {
        $this
            ->abstractRepositoryBeginTransactionMethod
            ->invoke($this->abstractRepository, $this->connection)
        ;

        $this->connection->expects($this->once())->method('commit');

        $abstractRepository = $this
            ->abstractRepositoryCommitMethod
            ->invoke($this->abstractRepository, $this->connection)
        ;

        $this->assertTrue($abstractRepository instanceof $this->abstractRepository);
    }

    public function testCommitWhenNotInTransaction()
    {
        $this->connection->expects($this->never())->method('commit');

        $abstractRepository = $this
            ->abstractRepositoryCommitMethod
            ->invoke($this->abstractRepository, $this->connection)
        ;

        $this->assertTrue($abstractRepository instanceof $this->abstractRepository);
    }

    public function testCommitWithoutConnection()
    {
        $this
            ->abstractRepositoryBeginTransactionMethod
            ->invoke($this->abstractRepository, $this->connection)
        ;

        $this->connection->expects($this->once())->method('commit');

        $abstractRepository = $this
            ->abstractRepositoryCommitMethod
            ->invoke($this->abstractRepository)
        ;

        $this->assertTrue($abstractRepository instanceof $this->abstractRepository);
    }

    public function testRollBack()
    {
        $this->connection->expects($this->once())->method('inTransaction')->willReturn(true);
        $this->connection->expects($this->once())->method('rollBack');

        $abstractRepository = $this
            ->abstractRepositoryRollBackMethod
            ->invoke($this->abstractRepository, $this->connection)
        ;

        $this->assertTrue($abstractRepository instanceof $this->abstractRepository);
    }

    public function testRollBackWhenNotInTransaction()
    {
        $this->connection->expects($this->once())->method('inTransaction')->willReturn(false);
        $this->connection->expects($this->never())->method('rollBack');

        $abstractRepository = $this
            ->abstractRepositoryRollBackMethod
            ->invoke($this->abstractRepository, $this->connection)
        ;

        $this->assertTrue($abstractRepository instanceof $this->abstractRepository);
    }

    public function testRollBackWithoutConnection()
    {
        $abstractRepository = $this
            ->abstractRepositoryRollBackMethod
            ->invoke($this->abstractRepository)
        ;

        $this->assertTrue($abstractRepository instanceof $this->abstractRepository);
    }

    public function testPrepareAndExecute()
    {
        $this->pdoStatement->expects($this->once())->method('execute');
        $this->connection->expects($this->once())->method('prepare')->willReturn($this->pdoStatement);
        $this->queryBuilder->expects($this->exactly(2))->method('buildQuery');

        $pdoStatement = $this
            ->abstractRepositoryPrepareAndExecuteMethod
            ->invoke($this->abstractRepository, $this->queryBuilder, [], $this->connection)
        ;

        $this->assertTrue($pdoStatement instanceof $this->pdoStatement);
    }

    public function testPrepareAndExecuteWithoutConnection()
    {
        $this->pdoStatement->expects($this->once())->method('execute');
        $this->connection->expects($this->once())->method('prepare')->willReturn($this->pdoStatement);
        $this->queryBuilder->expects($this->exactly(2))->method('buildQuery');

        $pdoStatement = $this
            ->abstractRepositoryPrepareAndExecuteMethod
            ->invoke($this->abstractRepository, $this->queryBuilder, [])
        ;

        $this->assertTrue($pdoStatement instanceof $this->pdoStatement);
    }

    public function testGetConnection()
    {
        $connection = $this->abstractRepositoryGetConnectionMethod->invoke($this->abstractRepository);

        $this->assertTrue($connection instanceof $this->connection);
    }

    public function testGetConnectionWithoutConnectionPool()
    {
        $this->abstractRepositoryConnectionPoolProperty->setValue($this->abstractRepository, null);

        $this->expectException(ConnectionInvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/Could not find connection by name "(.*)"\!/');

        $this->abstractRepositoryGetConnectionMethod->invoke($this->abstractRepository);
    }

    public function testGetModelClassConstant()
    {
        $constantValue = $this
            ->abstractRepositoryGetModelConstantMethod
            ->invoke($this->abstractRepository, static::CONSTANT_NAME)
        ;

        $this->assertEquals(static::CONSTANT, $constantValue);
    }
}
