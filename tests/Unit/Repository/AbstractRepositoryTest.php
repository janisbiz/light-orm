<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Repository;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Connection\ConnectionInvalidArgumentException;
use Janisbiz\LightOrm\ConnectionPool;
use Janisbiz\LightOrm\Paginator\PaginatorInterface;
use Janisbiz\LightOrm\QueryBuilder\QueryBuilderInterface;
use Janisbiz\LightOrm\Repository\AbstractRepository;
use Janisbiz\LightOrm\Repository\RepositoryException;
use Janisbiz\LightOrm\Tests\Unit\ReflectionTrait;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;

class AbstractRepositoryTest extends TestCase
{
    use ReflectionTrait;

    const DATABASE_NAME = 'database_name';

    const CONSTANT = 'constant_value';
    const CONSTANT_NAME = 'CONSTANT';
    const CONSTANT_NAME_NON_EXISTENT = 'CONSTANT_NON_EXISTENT';

    const VALUE_INVALID = [];

    const QUERY_BUILDER_COUNT_RESULT = 5;

    const PAGINATOR_PAGE = 1;
    const PAGINATOR_PAGE_SIZE = 2;
    const PAGINATOR_PAGE_SIZE_INVALID = 0;
    const PAGINATOR_PAGE_SIZE_FIRST_PAGE = 1;

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
     * @var AbstractRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $abstractRepository;

    /**
     * @var \ReflectionMethod
     */
    private $abstractRepositoryQuoteMethod;

    /**
     * @var \ReflectionMethod
     */
    private $abstractRepositoryPaginatorMethod;

    /**
     * @var \ReflectionMethod
     */
    private $abstractRepositoryLogMethod;

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
            true,
            [
                'getModelClass',
            ]
        );
        $this->abstractRepository->method('getModelClass')->willReturn(static::class);

        $this->abstractRepositoryQuoteMethod = $this->createAccessibleMethod($this->abstractRepository, 'quote');
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
        $this->abstractRepositoryGetModelConstantMethod = $this
            ->createAccessibleMethod($this->abstractRepository, 'getModelClassConstant')
        ;

        $this->connectionPool = $this->createMock(ConnectionPool::class);
        $this->connectionPool->method('getConnection')->willReturn($this->connection);

        $this->abstractRepositoryConnectionPoolProperty = $this
            ->createAccessibleProperty($this->abstractRepository, 'connectionPool')
        ;
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

        $this->abstractRepositoryQuoteMethod->invoke($this->abstractRepository, $value);
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

        $this->abstractRepositoryQuoteMethod->invoke($this->abstractRepository, static::VALUE_INVALID);
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
            ->invoke($this->abstractRepository)
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

        $this->assertTrue($this->abstractRepositoryCommitMethod->invoke($this->abstractRepository));
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
