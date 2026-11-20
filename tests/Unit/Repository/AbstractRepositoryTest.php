<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Repository;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Connection\ConnectionInvalidArgumentException;
use Janisbiz\LightOrm\ConnectionPool;
use Janisbiz\LightOrm\Paginator\PaginatorInterface;
use Janisbiz\LightOrm\QueryBuilder\QueryBuilderInterface;
use Janisbiz\LightOrm\Repository\AbstractRepository;
use Janisbiz\LightOrm\Tests\Unit\ReflectionTrait;
use PDOStatement;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;
use ReflectionMethod;
use ReflectionProperty;

#[AllowMockObjectsWithoutExpectations]
class AbstractRepositoryTest extends TestCase
{
    use ReflectionTrait;

    const string DATABASE_NAME = 'database_name';

    const string CONSTANT = 'constant_value';
    const string CONSTANT_NAME = 'CONSTANT';
    const string CONSTANT_NAME_NON_EXISTENT = 'CONSTANT_NON_EXISTENT';

    const array VALUE_INVALID = [];

    const int QUERY_BUILDER_COUNT_RESULT = 5;

    const int PAGINATOR_PAGE = 1;
    const int PAGINATOR_PAGE_INVALID = 0;
    const int PAGINATOR_PAGE_FIRST_PAGE = 1;
    const int PAGINATOR_PAGE_SIZE = 2;
    const int PAGINATOR_PAGE_SIZE_INVALID = 0;
    const int PAGINATOR_PAGE_SIZE_FIRST_PAGE = 1;

    private ConnectionInterface|MockObject $connection;

    private PDOStatement|MockObject $pdoStatement;

    private QueryBuilderInterface|MockObject $queryBuilder;

    private AbstractRepository|MockObject $abstractRepository;

    private ReflectionMethod $abstractRepositoryPaginatorMethod;

    private ReflectionMethod $abstractRepositoryLogMethod;

    private ReflectionMethod $abstractRepositoryBeginTransactionMethod;

    private ReflectionMethod $abstractRepositoryCommitMethod;

    private ReflectionMethod $abstractRepositoryRollBackMethod;

    private ReflectionMethod $abstractRepositoryPrepareAndExecuteMethod;

    private ReflectionMethod $abstractRepositoryGetConnectionMethod;

    private ReflectionMethod $abstractRepositoryGetEntityClassConstantMethod;

    private ConnectionPool|MockObject $connectionPool;

    private ReflectionProperty $abstractRepositoryConnectionPoolProperty;

    public function setUp(): void
    {
        $this->connection = $this->createMock(ConnectionInterface::class);

        $this->pdoStatement = $this->createMock(PDOStatement::class);

        $this->queryBuilder = $this->createMock(QueryBuilderInterface::class);

        $this->abstractRepository = $this->createPartialMock(
            AbstractRepository::class,
            ['getEntityClass', 'addPaginateQuery', 'createQueryBuilder', 'getPaginateResult'],
        );
            
        $this->abstractRepository->method('getEntityClass')->willReturn(static::class);

        $this->abstractRepositoryPaginatorMethod = $this
            ->createAccessibleMethod($this->abstractRepository, 'paginator')
        ;
        $this->abstractRepositoryLogMethod = $this->createAccessibleMethod($this->abstractRepository, 'log');
        $this->abstractRepositoryBeginTransactionMethod = $this
            ->createAccessibleMethod($this->abstractRepository, 'beginTransaction')
        ;
        $this->abstractRepositoryCommitMethod = $this->createAccessibleMethod($this->abstractRepository, 'commit');
        $this->abstractRepositoryRollBackMethod = $this->createAccessibleMethod($this->abstractRepository, 'rollBack');
        $this->abstractRepositoryPrepareAndExecuteMethod = $this
            ->createAccessibleMethod($this->abstractRepository, 'prepareAndExecute')
        ;
        $this->abstractRepositoryGetConnectionMethod = $this
            ->createAccessibleMethod($this->abstractRepository, 'getConnection')
        ;
        $this->abstractRepositoryGetEntityClassConstantMethod = $this
            ->createAccessibleMethod($this->abstractRepository, 'getEntityClassConstant')
        ;

        $this->connectionPool = $this->createStub(ConnectionPool::class);
        $this->connectionPool->method('getConnection')->willReturn($this->connection);

        $this->abstractRepositoryConnectionPoolProperty = $this
            ->createAccessibleProperty($this->abstractRepository, 'connectionPool')
        ;
        $this->abstractRepositoryConnectionPoolProperty->setValue($this->abstractRepository, $this->connectionPool);
    }

    public function testPaginator()
    {
        $this->queryBuilder->method('count')->willReturn(static::QUERY_BUILDER_COUNT_RESULT);

        $paginator = $this->abstractRepositoryPaginatorMethod->invoke(
            $this->abstractRepository,
            $this->queryBuilder,
            static::PAGINATOR_PAGE_SIZE,
            static::PAGINATOR_PAGE
        );

        $this->assertTrue($paginator instanceof PaginatorInterface);

        $this->abstractRepository->expects($this->once())->method('addPaginateQuery');
        $this->abstractRepository->expects($this->once())->method('getPaginateResult');

        $paginator->paginate();
    }

    public function testPaginatorWhenCurrentPageSizeIsLessThanOne()
    {
        $paginator = $this->abstractRepositoryPaginatorMethod->invoke(
            $this->abstractRepository,
            $this->queryBuilder,
            static::PAGINATOR_PAGE_SIZE_INVALID,
            static::PAGINATOR_PAGE
        );

        $this->assertTrue($paginator instanceof PaginatorInterface);

        $paginator->paginateFake();

        $this->assertEquals(static::PAGINATOR_PAGE_SIZE_FIRST_PAGE, $paginator->getPageSize());
    }

    public function testPaginatorWhenCurrentPageIsLessThanOne()
    {
        $paginator = $this->abstractRepositoryPaginatorMethod->invoke(
            $this->abstractRepository,
            $this->queryBuilder,
            static::PAGINATOR_PAGE_SIZE,
            static::PAGINATOR_PAGE_INVALID
        );

        $this->assertTrue($paginator instanceof PaginatorInterface);

        $paginator->paginateFake();

        $this->assertEquals(static::PAGINATOR_PAGE_FIRST_PAGE, $paginator->getCurrentPageNumber());
    }

    public function testLog()
    {
        $this->abstractRepository->setLogger(new NullLogger());
        $abstractRepository = $this->abstractRepositoryLogMethod->invoke(
            $this->abstractRepository,
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
        $abstractRepository = $this->abstractRepositoryLogMethod->invoke(
            $this->abstractRepository,
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
            ->invoke($this->abstractRepository, $this->connection)
        ;

        $this->assertTrue($abstractRepository instanceof $this->abstractRepository);
    }

    public function testCommitWhenConnectionCommitIsSuccessful()
    {
        $this
            ->abstractRepositoryBeginTransactionMethod
            ->invoke($this->abstractRepository, $this->connection)
        ;

        $this->connection->method('commit')->willReturn(true);

        $this->assertTrue($this->abstractRepositoryCommitMethod->invoke($this->abstractRepository, $this->connection));
    }

    public function testCommitWhenConnectionCommitIsUnsuccessful()
    {
        $this
            ->abstractRepositoryBeginTransactionMethod
            ->invoke($this->abstractRepository, $this->connection)
        ;

        $this->connection->method('commit')->willReturn(false);

        $this->assertFalse($this->abstractRepositoryCommitMethod->invoke($this->abstractRepository, $this->connection));
    }

    public function testCommitWhenNotInTransaction()
    {
        $this->connection->expects($this->never())->method('commit');

        $this->assertTrue($this->abstractRepositoryCommitMethod->invoke($this->abstractRepository, $this->connection));
    }

    public function testCommitWithoutConnection()
    {
        $this
            ->abstractRepositoryBeginTransactionMethod
            ->invoke($this->abstractRepository, $this->connection)
        ;

        $this->connection->expects($this->once())->method('commit')->willReturn(true);

        $this->assertTrue($this->abstractRepositoryCommitMethod->invoke($this->abstractRepository, $this->connection));
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
            ->invoke($this->abstractRepository, $this->connection)
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
            ->invoke($this->abstractRepository, $this->queryBuilder, [], $this->connection)
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
        $this->abstractRepositoryConnectionPoolProperty->setValue($this->abstractRepository, new ConnectionPool());

        $this->expectException(ConnectionInvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Could not find connection by name "(.*)"\!/');

        $this->abstractRepositoryGetConnectionMethod->invoke($this->abstractRepository);
    }

    public function testGetModelClassConstant()
    {
        $constantValue = $this
            ->abstractRepositoryGetEntityClassConstantMethod
            ->invoke($this->abstractRepository, static::CONSTANT_NAME)
        ;

        $this->assertEquals(static::CONSTANT, $constantValue);
    }
}
