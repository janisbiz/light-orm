<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\Repository;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Dms\MySQL\Enum\CommandEnum;
use Janisbiz\LightOrm\Dms\MySQL\Repository\AbstractRepository;
use Janisbiz\LightOrm\Dms\MySQL\Repository\RepositoryException;
use Janisbiz\LightOrm\Entity\BaseEntity;
use Janisbiz\LightOrm\Entity\EntityInterface;
use Janisbiz\LightOrm\Generator\Writer\WriterInterface;
use Janisbiz\LightOrm\QueryBuilder\QueryBuilderInterface;
use Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\Generator\Dms\DmsTableTest;
use Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\QueryBuilderTrait;
use Janisbiz\LightOrm\Tests\Unit\ReflectionTrait;
use PHPUnit\Framework\TestCase;

class AbstractRepositoryTest extends TestCase
{
    use QueryBuilderTrait;
    use ReflectionTrait;

    const COLUMN_AUTO_INCREMENT = 'col_a_i';
    const COLUMN_AUTO_INCREMENT_VALUE = 1;
    const COLUMN_ONE = 'col_1';
    const COLUMN_ONE_UPDATE_VALUE = 'val1Update';

    const TABLE = 'table';

    const RESULT_COUNT = 3;

    const PAGINATE_CURRENT_PAGE = 2;
    const PAGINATE_PAGE_SIZE = 10;

    /**
     * @var \PDOStatement|\PHPUnit_Framework_MockObject_MockObject
     */
    private $statement;

    /**
     * @var ConnectionInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $connection;

    /**
     * @var EntityInterface
     */
    private $entity;

    /**
     * @var \ArrayObject|array
     */
    private $dataOriginal = [
        self::COLUMN_AUTO_INCREMENT => null,
        self::COLUMN_ONE => 'val1',
        'col_2' => 2,
        'col_3' => 3.3,
    ];

    /**
     * @var \ArrayObject
     */
    private $data;

    /**
     * @var AbstractRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $abstractRepository;

    public function setUp()
    {
        $this->statement = $this->createMock(\PDOStatement::class);

        $this->connection = $this->createPartialMock(
            ConnectionInterface::class,
            [
                'beginTransaction',
                'commit',
                'inTransaction',
                'lastInsertId',
                'prepare',
                'rollBack',
                'setSqlSafeUpdates',
                'unsetSqlSafeUpdates',
            ]
        );
        $this->connection->method('prepare')->willReturn($this->statement);

        $this->entity = $this->createEntity();

        $this->abstractRepository = $this->getMockForAbstractClass(
            AbstractRepository::class,
            [],
            '',
            true,
            true,
            true,
            [
                'beginTransaction',
                'commit',
                'getConnection',
                'getEntityClassConstant',
                'prepareAndExecute',
                'rollBack',
            ]
        );
        $this->abstractRepository
            ->expects($this->any())
            ->method('getConnection')
            ->willReturn($this->connection)
        ;
    }

    public function testInsert()
    {
        $queryBuilder = $this
            ->createQueryBuilder($this->abstractRepository, $this->entity)
            ->command(CommandEnum::INSERT_INTO)
        ;

        $this->abstractRepository->expects($this->once())->method('commit');
        $this->abstractRepository->expects($this->once())->method('prepareAndExecute');

        $this->connection->method('lastInsertId')->willReturn(static::COLUMN_AUTO_INCREMENT_VALUE);

        $entity = $this
            ->createAccessibleMethod($this->abstractRepository, 'insert')
            ->invoke($this->abstractRepository, $queryBuilder)
        ;

        static::assertTrue($entity instanceof $this->entity);
        static::assertEquals(static::COLUMN_AUTO_INCREMENT_VALUE, $entity->data()[static::COLUMN_AUTO_INCREMENT]);
    }

    public function testInsertWithSqlException()
    {
        $queryBuilder = $this
            ->createQueryBuilder($this->abstractRepository, $this->entity)
            ->command(CommandEnum::INSERT_INTO)
        ;

        $this->abstractRepository->expects($this->once())->method('beginTransaction');
        $this->abstractRepository
            ->expects($this->once())
            ->method('prepareAndExecute')
            ->willThrowException(new \Exception('PDO Exception'))
        ;
        $this->abstractRepository->expects($this->once())->method('rollBack');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('PDO Exception');

        $this
            ->createAccessibleMethod($this->abstractRepository, 'insert')
            ->invoke($this->abstractRepository, $queryBuilder)
        ;
    }

    public function testInsertToString()
    {
        $queryBuilder = $this
            ->createQueryBuilder($this->abstractRepository, $this->entity)
            ->command(CommandEnum::INSERT_INTO)
        ;

        $insertQuery = $this
            ->createAccessibleMethod($this->abstractRepository, 'insert')
            ->invoke($this->abstractRepository, $queryBuilder, true)
        ;

        $this->assertTrue(\is_string($insertQuery));
        $this->assertStringStartsWith(CommandEnum::INSERT_INTO, $insertQuery);
    }

    public function testInsertWithoutEntity()
    {
        $this->expectException(RepositoryException::class);
        $this->expectExceptionMessage(
            'Cannot perform insert on query without entity! Please create query builder with entity.'
        );

        $queryBuilder = $this->createQueryBuilder($this->abstractRepository);

        $this
            ->createAccessibleMethod($this->abstractRepository, 'insert')
            ->invoke($this->abstractRepository, $queryBuilder)
        ;
    }

    public function testInsertIgnore()
    {
        $queryBuilder = $this
            ->createQueryBuilder($this->abstractRepository, $this->entity)
            ->command(CommandEnum::INSERT_IGNORE_INTO)
        ;

        $this->abstractRepository->expects($this->once())->method('commit');
        $this->abstractRepository->expects($this->once())->method('prepareAndExecute');

        $this->connection->method('lastInsertId')->willReturn(static::COLUMN_AUTO_INCREMENT_VALUE);

        $entity = $this
            ->createAccessibleMethod($this->abstractRepository, 'insertIgnore')
            ->invoke($this->abstractRepository, $queryBuilder)
        ;

        static::assertTrue($entity instanceof $this->entity);
        static::assertEquals(static::COLUMN_AUTO_INCREMENT_VALUE, $entity->data()[static::COLUMN_AUTO_INCREMENT]);
    }

    public function testInsertIgnoreToString()
    {
        $queryBuilder = $this
            ->createQueryBuilder($this->abstractRepository, $this->entity)
            ->command(CommandEnum::INSERT_IGNORE_INTO)
        ;

        $insertIgnoreQuery = $this
            ->createAccessibleMethod($this->abstractRepository, 'insertIgnore')
            ->invoke($this->abstractRepository, $queryBuilder, true)
        ;

        $this->assertTrue(\is_string($insertIgnoreQuery));
        $this->assertStringStartsWith(CommandEnum::INSERT_IGNORE_INTO, $insertIgnoreQuery);
    }

    public function testReplace()
    {
        $queryBuilder = $this
            ->createQueryBuilder($this->abstractRepository, $this->entity)
            ->command(CommandEnum::REPLACE_INTO)
        ;

        $this->abstractRepository->expects($this->once())->method('commit');
        $this->abstractRepository->expects($this->once())->method('prepareAndExecute');

        $this->connection->method('lastInsertId')->willReturn(static::COLUMN_AUTO_INCREMENT_VALUE);

        $entity = $this
            ->createAccessibleMethod($this->abstractRepository, 'replace')
            ->invoke($this->abstractRepository, $queryBuilder)
        ;

        static::assertTrue($entity instanceof $this->entity);
        static::assertEquals(static::COLUMN_AUTO_INCREMENT_VALUE, $entity->data()[static::COLUMN_AUTO_INCREMENT]);
    }

    public function testReplaceToString()
    {
        $queryBuilder = $this
            ->createQueryBuilder($this->abstractRepository, $this->entity)
            ->command(CommandEnum::REPLACE_INTO)
        ;

        $replaceQuery = $this
            ->createAccessibleMethod($this->abstractRepository, 'replace')
            ->invoke($this->abstractRepository, $queryBuilder, true)
        ;

        $this->assertTrue(\is_string($replaceQuery));
        $this->assertStringStartsWith(CommandEnum::REPLACE_INTO, $replaceQuery);
    }

    public function testFindOne()
    {
        $queryBuilder = $this
            ->createQueryBuilder($this->abstractRepository, $this->entity)
            ->command(CommandEnum::SELECT)
        ;

        $this->statement->expects($this->once())->method('fetch')->willReturn($this->entity);
        $this
            ->statement
            ->expects($this->once())
            ->method('setFetchMode')
            ->with(
                \PDO::FETCH_CLASS,
                \get_class($this->entity),
                [
                    false,
                ]
            )
        ;

        $this->abstractRepository->expects($this->once())->method('prepareAndExecute')->willReturn($this->statement);

        $entity = $this
            ->createAccessibleMethod($this->abstractRepository, 'findOne')
            ->invoke($this->abstractRepository, $queryBuilder)
        ;

        $this->assertTrue($entity instanceof $this->entity);
    }

    public function testFindOneToString()
    {
        $queryBuilder = $this
            ->createQueryBuilder($this->abstractRepository, $this->entity)
            ->command(CommandEnum::SELECT)
        ;

        $findOneQuery = $this
            ->createAccessibleMethod($this->abstractRepository, 'findOne')
            ->invoke($this->abstractRepository, $queryBuilder, true)
        ;

        $this->assertTrue(\is_string($findOneQuery));
        $this->assertStringStartsWith(CommandEnum::SELECT, $findOneQuery);
    }

    public function testFindOneWithException()
    {
        $queryBuilder = $this
            ->createQueryBuilder($this->abstractRepository, $this->entity)
            ->command(CommandEnum::SELECT)
        ;

        $this->abstractRepository
            ->expects($this->once())
            ->method('prepareAndExecute')
            ->willThrowException(new \Exception('PDO Exception'))
        ;

        $this->abstractRepository->expects($this->once())->method('rollBack');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('PDO Exception');

        $this
            ->createAccessibleMethod($this->abstractRepository, 'findOne')
            ->invoke($this->abstractRepository, $queryBuilder)
        ;
    }

    public function testFind()
    {
        $queryBuilder = $this
            ->createQueryBuilder($this->abstractRepository, $this->entity)
            ->command(CommandEnum::SELECT)
        ;

        $entityArray = [
            $this->entity,
            $this->entity,
            $this->entity,
        ];
        $this->statement->expects($this->once())->method('fetchAll')->willReturn($entityArray);
        $this
            ->statement
            ->expects($this->once())
            ->method('setFetchMode')
            ->with(
                \PDO::FETCH_CLASS,
                \get_class($this->entity),
                [
                    false,
                ]
            )
        ;

        $this->abstractRepository->expects($this->once())->method('prepareAndExecute')->willReturn($this->statement);

        $resultArray = $this
            ->createAccessibleMethod($this->abstractRepository, 'find')
            ->invoke($this->abstractRepository, $queryBuilder)
        ;

        $this->assertCount(\count($entityArray), $resultArray);
        foreach ($resultArray as $resultEntity) {
            $this->assertTrue($resultEntity instanceof $this->entity);
        }
    }

    public function testFindToString()
    {
        $queryBuilder = $this
            ->createQueryBuilder($this->abstractRepository, $this->entity)
            ->command(CommandEnum::SELECT)
        ;

        $findQuery = $this
            ->createAccessibleMethod($this->abstractRepository, 'find')
            ->invoke($this->abstractRepository, $queryBuilder, true)
        ;

        $this->assertTrue(\is_string($findQuery));
        $this->assertStringStartsWith(CommandEnum::SELECT, $findQuery);
    }

    public function testFindWithException()
    {
        $queryBuilder = $this
            ->createQueryBuilder($this->abstractRepository, $this->entity)
            ->command(CommandEnum::SELECT)
        ;

        $this->abstractRepository
            ->expects($this->once())
            ->method('prepareAndExecute')
            ->willThrowException(new \Exception('PDO Exception'))
        ;

        $this->abstractRepository->expects($this->once())->method('rollBack');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('PDO Exception');

        $this
            ->createAccessibleMethod($this->abstractRepository, 'find')
            ->invoke($this->abstractRepository, $queryBuilder)
        ;
    }

    public function testUpdate()
    {
        $this
            ->entity
            ->setIsNew(false)
            ->setIsSaved(true)
            ->setColAI(static::COLUMN_AUTO_INCREMENT_VALUE)
            ->setCol1(static::COLUMN_ONE_UPDATE_VALUE)
            ->setDataOriginal(static::COLUMN_AUTO_INCREMENT, static::COLUMN_AUTO_INCREMENT_VALUE)
        ;

        $queryBuilder = $this
            ->createQueryBuilder($this->abstractRepository, $this->entity)
            ->command(CommandEnum::UPDATE)
        ;

        $this->connection->expects($this->once())->method('setSqlSafeUpdates');
        $this->connection->expects($this->once())->method('unsetSqlSafeUpdates');

        $this->abstractRepository->expects($this->once())->method('prepareAndExecute');
        $this->abstractRepository->expects($this->once())->method('commit')->willReturn(true);

        $entity = $this
            ->createAccessibleMethod($this->abstractRepository, 'update')
            ->invoke($this->abstractRepository, $queryBuilder)
        ;

        $this->assertTrue($entity instanceof $this->entity);

        $this->assertEquals($entity->data(static::COLUMN_AUTO_INCREMENT), static::COLUMN_AUTO_INCREMENT_VALUE);
        $this->assertEquals(
            $entity->dataOriginal()[static::COLUMN_AUTO_INCREMENT],
            static::COLUMN_AUTO_INCREMENT_VALUE
        );
    }

    public function testUpdateWithoutEntity()
    {
        $queryBuilder = $this
            ->createQueryBuilder($this->abstractRepository)
            ->command(CommandEnum::UPDATE)
            ->set(static::COLUMN_ONE, static::COLUMN_ONE_UPDATE_VALUE)
            ->where(\sprintf('%s = %d', static::COLUMN_AUTO_INCREMENT, static::COLUMN_AUTO_INCREMENT_VALUE))
        ;

        $this->connection->expects($this->once())->method('setSqlSafeUpdates');
        $this->connection->expects($this->once())->method('unsetSqlSafeUpdates');

        $this->abstractRepository->expects($this->once())->method('prepareAndExecute');
        $this->abstractRepository->expects($this->once())->method('commit')->willReturn(true);

        $this->assertTrue(
            $this
                ->createAccessibleMethod($this->abstractRepository, 'update')
                ->invoke($this->abstractRepository, $queryBuilder)
        );
    }

    public function testUpdateWithoutEntityChanges()
    {
        $queryBuilder = $this
            ->createQueryBuilder($this->abstractRepository, $this->entity)
            ->command(CommandEnum::UPDATE)
        ;

        $this->connection->expects($this->never())->method('setSqlSafeUpdates');
        $this->connection->expects($this->never())->method('unsetSqlSafeUpdates');

        $this->abstractRepository->expects($this->never())->method('prepareAndExecute');
        $this->abstractRepository->expects($this->never())->method('commit');

        $entity = $this
            ->createAccessibleMethod($this->abstractRepository, 'update')
            ->invoke($this->abstractRepository, $queryBuilder)
        ;

        $this->assertTrue($entity instanceof $this->entity);
        $this->assertEquals($this->entity, $entity);
    }

    public function testUpdateToString()
    {
        $this
            ->entity
            ->setIsNew(false)
            ->setIsSaved(true)
            ->setColAI(static::COLUMN_AUTO_INCREMENT_VALUE)
            ->setCol1(static::COLUMN_ONE_UPDATE_VALUE)
            ->setDataOriginal(static::COLUMN_AUTO_INCREMENT, static::COLUMN_AUTO_INCREMENT_VALUE)
        ;

        $queryBuilder = $this
            ->createQueryBuilder($this->abstractRepository, $this->entity)
            ->command(CommandEnum::UPDATE)
        ;

        $updateQuery = $this
            ->createAccessibleMethod($this->abstractRepository, 'update')
            ->invoke($this->abstractRepository, $queryBuilder, true)
        ;

        $this->assertTrue(\is_string($updateQuery));
        $this->assertStringStartsWith(CommandEnum::UPDATE, $updateQuery);
    }

    public function testUpdateWithException()
    {
        $this
            ->entity
            ->setIsNew(false)
            ->setIsSaved(true)
            ->setColAI(static::COLUMN_AUTO_INCREMENT_VALUE)
            ->setCol1(static::COLUMN_ONE_UPDATE_VALUE)
            ->setDataOriginal(static::COLUMN_AUTO_INCREMENT, static::COLUMN_AUTO_INCREMENT_VALUE)
        ;

        $queryBuilder = $this
            ->createQueryBuilder($this->abstractRepository, $this->entity)
            ->command(CommandEnum::UPDATE)
        ;

        $this->connection->expects($this->once())->method('setSqlSafeUpdates');
        $this->connection->expects($this->once())->method('unsetSqlSafeUpdates');

        $this->abstractRepository
            ->expects($this->once())
            ->method('prepareAndExecute')
            ->willThrowException(new \Exception('PDO Exception'))
        ;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('PDO Exception');

        $this
            ->createAccessibleMethod($this->abstractRepository, 'update')
            ->invoke($this->abstractRepository, $queryBuilder)
        ;
    }

    public function testUpdateIgnore()
    {
        $this
            ->entity
            ->setIsNew(false)
            ->setIsSaved(true)
            ->setColAI(static::COLUMN_AUTO_INCREMENT_VALUE)
            ->setCol1(static::COLUMN_ONE_UPDATE_VALUE)
            ->setDataOriginal(static::COLUMN_AUTO_INCREMENT, static::COLUMN_AUTO_INCREMENT_VALUE)
        ;

        $queryBuilder = $this
            ->createQueryBuilder($this->abstractRepository, $this->entity)
            ->command(CommandEnum::UPDATE_IGNORE)
        ;

        $this->connection->expects($this->once())->method('setSqlSafeUpdates');
        $this->connection->expects($this->once())->method('unsetSqlSafeUpdates');

        $this->abstractRepository->expects($this->once())->method('prepareAndExecute');
        $this->abstractRepository->expects($this->once())->method('commit')->willReturn(true);

        $entity = $this
            ->createAccessibleMethod($this->abstractRepository, 'updateIgnore')
            ->invoke($this->abstractRepository, $queryBuilder)
        ;

        $this->assertTrue($entity instanceof $this->entity);
        $this->assertEquals($entity->data()[static::COLUMN_AUTO_INCREMENT], static::COLUMN_AUTO_INCREMENT_VALUE);
        $this->assertEquals(
            $entity->dataOriginal()[static::COLUMN_AUTO_INCREMENT],
            static::COLUMN_AUTO_INCREMENT_VALUE
        );
    }

    public function testUpdateIgnoreToString()
    {
        $this
            ->entity
            ->setIsNew(false)
            ->setIsSaved(true)
            ->setColAI(static::COLUMN_AUTO_INCREMENT_VALUE)
            ->setCol1(static::COLUMN_ONE_UPDATE_VALUE)
            ->setDataOriginal(static::COLUMN_AUTO_INCREMENT, static::COLUMN_AUTO_INCREMENT_VALUE)
        ;

        $queryBuilder = $this
            ->createQueryBuilder($this->abstractRepository, $this->entity)
            ->command(CommandEnum::UPDATE_IGNORE)
        ;

        $updateIgnoreQuery = $this
            ->createAccessibleMethod($this->abstractRepository, 'updateIgnore')
            ->invoke($this->abstractRepository, $queryBuilder, true)
        ;

        $this->assertTrue(\is_string($updateIgnoreQuery));
        $this->assertStringStartsWith(CommandEnum::UPDATE_IGNORE, $updateIgnoreQuery);
    }

    public function testDelete()
    {
        $this
            ->entity
            ->setIsNew(false)
            ->setIsSaved(true)
            ->setColAI(static::COLUMN_AUTO_INCREMENT_VALUE)
        ;

        $queryBuilder = $this
            ->createQueryBuilder($this->abstractRepository, $this->entity)
            ->command(CommandEnum::DELETE)
        ;

        $this->connection->expects($this->once())->method('setSqlSafeUpdates');
        $this->connection->expects($this->once())->method('unsetSqlSafeUpdates');

        $this->abstractRepository->expects($this->once())->method('prepareAndExecute');
        $this->abstractRepository->expects($this->once())->method('commit')->willReturn(true);

        $this->assertTrue(
            $this
                ->createAccessibleMethod($this->abstractRepository, 'delete')
                ->invoke($this->abstractRepository, $queryBuilder)
        );
    }

    public function testDeleteWithoutEntity()
    {
        $queryBuilder = $this
            ->createQueryBuilder($this->abstractRepository)
            ->command(CommandEnum::DELETE)
            ->where(\sprintf('%s = %d', static::COLUMN_AUTO_INCREMENT, static::COLUMN_AUTO_INCREMENT_VALUE))
        ;

        $this->connection->expects($this->once())->method('setSqlSafeUpdates');
        $this->connection->expects($this->once())->method('unsetSqlSafeUpdates');

        $this->abstractRepository->expects($this->once())->method('prepareAndExecute');
        $this->abstractRepository->expects($this->once())->method('commit')->willReturn(true);

        $this->assertTrue(
            $this
                ->createAccessibleMethod($this->abstractRepository, 'delete')
                ->invoke($this->abstractRepository, $queryBuilder)
        );
    }

    public function testDeleteWithEntityWithoutPrimaryKeys()
    {
        $queryBuilder = $this
            ->createQueryBuilder($this->abstractRepository, $this->entity)
            ->command(CommandEnum::DELETE)
        ;

        $this->assertFalse(
            $this
                ->createAccessibleMethod($this->abstractRepository, 'delete')
                ->invoke($this->abstractRepository, $queryBuilder)
        );
    }

    public function testDeleteToString()
    {
        $this
            ->entity
            ->setIsNew(false)
            ->setIsSaved(true)
            ->setColAI(static::COLUMN_AUTO_INCREMENT_VALUE)
        ;

        $queryBuilder = $this
            ->createQueryBuilder($this->abstractRepository, $this->entity)
            ->command(CommandEnum::DELETE)
        ;

        $deleteQuery = $this
            ->createAccessibleMethod($this->abstractRepository, 'delete')
            ->invoke($this->abstractRepository, $queryBuilder, true)
        ;

        $this->assertTrue(\is_string($deleteQuery));
        $this->assertStringStartsWith(CommandEnum::DELETE, $deleteQuery);
    }

    public function testDeleteWithException()
    {
        $this
            ->entity
            ->setIsNew(false)
            ->setIsSaved(true)
            ->setColAI(static::COLUMN_AUTO_INCREMENT_VALUE)
        ;

        $queryBuilder = $this
            ->createQueryBuilder($this->abstractRepository, $this->entity)
            ->command(CommandEnum::DELETE)
        ;

        $this->connection->expects($this->once())->method('setSqlSafeUpdates');
        $this->connection->expects($this->once())->method('unsetSqlSafeUpdates');

        $this->abstractRepository
            ->expects($this->once())
            ->method('prepareAndExecute')
            ->willThrowException(new \Exception('PDO Exception'))
        ;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('PDO Exception');

        $this
            ->createAccessibleMethod($this->abstractRepository, 'delete')
            ->invoke($this->abstractRepository, $queryBuilder)
        ;
    }

    public function testCount()
    {
        $queryBuilder = $this->createQueryBuilder($this->abstractRepository)->command(CommandEnum::SELECT);

        $this->statement->expects($this->once())->method('fetchColumn')->with(0)->willReturn(static::RESULT_COUNT);

        $this->abstractRepository->expects($this->once())->method('prepareAndExecute')->willReturn($this->statement);
        $this->abstractRepository->method('getEntityClassConstant')->willReturn(static::TABLE);

        $resultCount = $this
            ->createAccessibleMethod($this->abstractRepository, 'count')
            ->invoke($this->abstractRepository, $queryBuilder)
        ;

        $this->assertEquals(static::RESULT_COUNT, $resultCount);
    }

    public function testCountWithWrongCommand()
    {
        $queryBuilder = $this->createQueryBuilder($this->abstractRepository)->command(CommandEnum::DELETE);

        $this->abstractRepository->method('getEntityClassConstant')->willReturn(static::TABLE);

        $this->expectException(RepositoryException::class);
        $this->expectExceptionMessage(
            'Command "DELETE" is not a valid command for count query! Use "SELECT" command to execute count query.'
        );

        $this
            ->createAccessibleMethod($this->abstractRepository, 'count')
            ->invoke($this->abstractRepository, $queryBuilder)
        ;
    }

    public function testCountToString()
    {
        $queryBuilder = $this->createQueryBuilder($this->abstractRepository)->command(CommandEnum::SELECT);

        $this->abstractRepository->method('getEntityClassConstant')->willReturn(static::TABLE);

        $countQuery = $this
            ->createAccessibleMethod($this->abstractRepository, 'count')
            ->invoke($this->abstractRepository, $queryBuilder, true)
        ;

        $this->assertTrue(\is_string($countQuery));
        $this->assertStringStartsWith(CommandEnum::SELECT, $countQuery);
    }

    public function testCountWithException()
    {
        $queryBuilder = $this->createQueryBuilder($this->abstractRepository)->command(CommandEnum::SELECT);

        $this->abstractRepository->method('getEntityClassConstant')->willReturn(static::TABLE);
        $this->abstractRepository
            ->expects($this->once())
            ->method('prepareAndExecute')
            ->willThrowException(new \Exception('PDO Exception'))
        ;

        $this->abstractRepository->expects($this->once())->method('rollBack');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('PDO Exception');

        $this
            ->createAccessibleMethod($this->abstractRepository, 'count')
            ->invoke($this->abstractRepository, $queryBuilder)
        ;
    }

    public function testCreateQueryBuilder()
    {
        $createQueryBuilderMethod = new \ReflectionMethod($this->abstractRepository, 'createQueryBuilder');
        $createQueryBuilderMethod->setAccessible(true);


        $this->abstractRepository
            ->expects($this->any())
            ->method('getEntityClassConstant')
            ->withConsecutive([WriterInterface::CLASS_CONSTANT_TABLE_NAME])
            ->willReturn(DmsTableTest::TABLE_NAME)
        ;

        $queryBuilder = $createQueryBuilderMethod->invoke($this->abstractRepository);

        $this->assertTrue($queryBuilder instanceof QueryBuilderInterface);
    }

    public function testAddPaginateQuery()
    {
        $queryBuilder = $this
            ->createQueryBuilder($this->abstractRepository, $this->entity)
            ->command(CommandEnum::SELECT)
        ;

        $this
            ->createAccessibleMethod($this->abstractRepository, 'addPaginateQuery')
            ->invoke(
                $this->abstractRepository,
                $queryBuilder,
                static::PAGINATE_PAGE_SIZE,
                static::PAGINATE_CURRENT_PAGE
            )
        ;

        $this->assertEquals(
            static::PAGINATE_PAGE_SIZE,
            $this->createAccessibleProperty($queryBuilder, 'limit')->getValue($queryBuilder)
        );
        $this->assertEquals(
            static::PAGINATE_PAGE_SIZE * static::PAGINATE_CURRENT_PAGE - static::PAGINATE_PAGE_SIZE,
            $this->createAccessibleProperty($queryBuilder, 'offset')->getValue($queryBuilder)
        );
    }

    public function testGetPaginateResult()
    {
        $queryBuilder = $this->createQueryBuilder($this->abstractRepository, $this->entity);

        $entityArray = [
            $this->entity,
            $this->entity,
            $this->entity,
        ];

        $this->statement->expects($this->once())->method('fetchAll')->willReturn($entityArray);
        $this
            ->statement
            ->expects($this->once())
            ->method('setFetchMode')
            ->with(
                \PDO::FETCH_CLASS,
                \get_class($this->entity),
                [
                    false,
                ]
            )
        ;

        $this->abstractRepository->expects($this->once())->method('prepareAndExecute')->willReturn($this->statement);

        $resultArray = $this
            ->createAccessibleMethod($this->abstractRepository, 'getPaginateResult')
            ->invoke($this->abstractRepository, $queryBuilder)
        ;

        $this->assertCount(\count($entityArray), $resultArray);
        foreach ($resultArray as $resultEntity) {
            $this->assertTrue($resultEntity instanceof $this->entity);
        }
    }

    /**
     * @return EntityInterface
     */
    private function createEntity()
    {
        return new class(
            $this->dataOriginal,
            \array_keys($this->dataOriginal),
            [
                AbstractRepositoryTest::COLUMN_AUTO_INCREMENT,
            ],
            [
                AbstractRepositoryTest::COLUMN_AUTO_INCREMENT,
            ]
        ) extends BaseEntity
        {
            const TABLE_NAME = AbstractRepositoryTest::TABLE;

            /**
             * @param array $dataOriginal
             * @param array $columns
             * @param array $primaryKeys
             * @param array $primaryKeysAutoIncrement
             */
            public function __construct(
                array &$dataOriginal,
                array $columns,
                array $primaryKeys,
                array $primaryKeysAutoIncrement
            ) {
                $this->dataOriginal = $this->data = $dataOriginal;
                $this->columns = $columns;
                $this->primaryKeys = $primaryKeys;
                $this->primaryKeysAutoIncrement = $primaryKeysAutoIncrement;
            }

            /**
             * @param bool $isNew
             *
             * @return $this
             */
            public function setIsNew(bool $isNew): BaseEntity
            {
                $this->isNew = $isNew;

                return $this;
            }

            /**
             * @param bool $isSaved
             *
             * @return $this
             */
            public function setIsSaved(bool $isSaved): BaseEntity
            {
                $this->isSaved = $isSaved;

                return $this;
            }

            /**
             * @param string $key
             * @param array|string|int|double|null $value
             *
             * @return $this
             */
            public function setDataOriginal(string $key, $value): BaseEntity
            {
                $this->dataOriginal[$key] = $value;

                return $this;
            }
        };
    }
}
