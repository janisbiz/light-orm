<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\Repository;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Dms\MySQL\Enum\CommandEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilder;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderInterface;
use Janisbiz\LightOrm\Dms\MySQL\Repository\AbstractRepository;
use Janisbiz\LightOrm\Entity\EntityInterface;
use PHPUnit\Framework\TestCase;

class AbstractRepositoryTest extends TestCase
{
    const COLUMN_AUTO_INCREMENT = 'colAI';
    const COLUMN_AUTO_INCREMENT_VALUE = 1;
    const COLUMN_ONE = 'col1';
    const COLUMN_ONE_UPDATE_VALUE = 'val1Update';

    const RESULT_COUNT = 3;

    /**
     * @var \PDOStatement|\PHPUnit_Framework_MockObject_MockObject
     */
    private $statement;

    /**
     * @var ConnectionInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $connection;

    /**
     * @var QueryBuilderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queryBuilder;

    /**
     * @var EntityInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entity;

    /**
     * @var \ArrayObject|array
     */
    private $data = [
        self::COLUMN_AUTO_INCREMENT => null,
        self::COLUMN_ONE => 'val1',
        'col2' => 2,
        'col3' => 3.3,
    ];

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
                'inTransaction',
                'prepare',
                'commit',
                'rollBack',
                'lastInsertId',
                'setSqlSafeUpdates',
                'unsetSqlSafeUpdates'
            ]
        );
        $this->connection->method('prepare')->willReturn($this->statement);

        $this->entity = $this->createMock(EntityInterface::class);
        $this->data = new \ArrayObject($this->data);
        $this->entity->method('data')->willReturnReference($this->data);
        $this->entity->method('columns')->willReturn(\array_keys($this->data->getArrayCopy()));
        $this
            ->entity
            ->method('primaryKeysAutoIncrement')
            ->willReturn([
                static::COLUMN_AUTO_INCREMENT,
            ])
        ;

        $this->abstractRepository = $this->getMockForAbstractClass(
            AbstractRepository::class,
            [],
            '',
            true,
            true,
            true,
            [
                'addEntityInsertQuery',
                'addEntityUpdateQuery',
                'addEntityDeleteQuery',
                'getConnection',
                'beginTransaction',
                'prepareAndExecute',
                'rollBack',
                'commit',
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
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository, $this->entity))
            ->command(CommandEnum::INSERT_INTO)
        ;

        $this->abstractRepository->expects($this->once())->method('addEntityInsertQuery');
        $this->abstractRepository->expects($this->once())->method('commit');
        $this->abstractRepository->expects($this->once())->method('prepareAndExecute');

        $this->connection->method('lastInsertId')->willReturn(static::COLUMN_AUTO_INCREMENT_VALUE);

        $entity = $this->abstractRepository->insert($this->queryBuilder);

        static::assertTrue($entity instanceof $this->entity);
        static::assertEquals(static::COLUMN_AUTO_INCREMENT_VALUE, $entity->data()[static::COLUMN_AUTO_INCREMENT]);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage PDO Exception
     */
    public function testInsertWithSqlException()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository, $this->entity))
            ->command(CommandEnum::INSERT_INTO)
        ;

        $this->abstractRepository->expects($this->once())->method('addEntityInsertQuery');
        $this->abstractRepository->expects($this->once())->method('beginTransaction');
        $this->abstractRepository
            ->expects($this->once())
            ->method('prepareAndExecute')
            ->willThrowException(new \Exception('PDO Exception'))
        ;
        $this->abstractRepository->expects($this->once())->method('rollBack');

        $this->abstractRepository->insert($this->queryBuilder);
    }

    public function testInsertToString()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository, $this->entity))
            ->command(CommandEnum::INSERT_INTO)
        ;

        $this->abstractRepository->expects($this->once())->method('addEntityInsertQuery');

        $insertQuery = $this->abstractRepository->insert($this->queryBuilder, true);

        $this->assertTrue(\is_string($insertQuery));
        $this->assertStringStartsWith(CommandEnum::INSERT_INTO, $insertQuery);
    }

    /**
     * @expectedException \Janisbiz\LightOrm\Dms\MySQL\Repository\RepositoryException
     * @expectedExceptionMessage Cannot perform insert on query without entity! Please create query builder with entity.
     */
    public function testInsertWithoutEntity()
    {
        $this->abstractRepository->insert(new QueryBuilder($this->abstractRepository));
    }

    public function testInsertIgnore()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository, $this->entity))
            ->command(CommandEnum::INSERT_IGNORE_INTO)
        ;

        $this->abstractRepository->expects($this->once())->method('addEntityInsertQuery');
        $this->abstractRepository->expects($this->once())->method('commit');
        $this->abstractRepository->expects($this->once())->method('prepareAndExecute');

        $this->connection->method('lastInsertId')->willReturn(static::COLUMN_AUTO_INCREMENT_VALUE);

        $entity = $this->abstractRepository->insertIgnore($this->queryBuilder);

        static::assertTrue($entity instanceof $this->entity);
        static::assertEquals(static::COLUMN_AUTO_INCREMENT_VALUE, $entity->data()[static::COLUMN_AUTO_INCREMENT]);
    }

    public function testInsertIgnoreToString()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository, $this->entity))
            ->command(CommandEnum::INSERT_IGNORE_INTO)
        ;

        $this->abstractRepository->expects($this->once())->method('addEntityInsertQuery');

        $insertIgnoreQuery = $this->abstractRepository->insertIgnore($this->queryBuilder, true);

        $this->assertTrue(\is_string($insertIgnoreQuery));
        $this->assertStringStartsWith(CommandEnum::INSERT_IGNORE_INTO, $insertIgnoreQuery);
    }

    public function testReplace()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository, $this->entity))
            ->command(CommandEnum::REPLACE_INTO)
        ;

        $this->abstractRepository->expects($this->once())->method('addEntityInsertQuery');
        $this->abstractRepository->expects($this->once())->method('commit');
        $this->abstractRepository->expects($this->once())->method('prepareAndExecute');

        $this->connection->method('lastInsertId')->willReturn(static::COLUMN_AUTO_INCREMENT_VALUE);

        $entity = $this->abstractRepository->replace($this->queryBuilder);

        static::assertTrue($entity instanceof $this->entity);
        static::assertEquals(static::COLUMN_AUTO_INCREMENT_VALUE, $entity->data()[static::COLUMN_AUTO_INCREMENT]);
    }

    public function testReplaceToString()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository, $this->entity))
            ->command(CommandEnum::REPLACE_INTO)
        ;

        $this->abstractRepository->expects($this->once())->method('addEntityInsertQuery');

        $replaceQuery = $this->abstractRepository->replace($this->queryBuilder, true);

        $this->assertTrue(\is_string($replaceQuery));
        $this->assertStringStartsWith(CommandEnum::REPLACE_INTO, $replaceQuery);
    }

    public function testFindOne()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository, $this->entity))
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

        $entity = $this->abstractRepository->findOne($this->queryBuilder);

        $this->assertTrue($entity instanceof $this->entity);
    }

    public function testFindOneToString()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository, $this->entity))
            ->command(CommandEnum::SELECT)
        ;

        $findOneQuery = $this->abstractRepository->findOne($this->queryBuilder, true);

        $this->assertTrue(\is_string($findOneQuery));
        $this->assertStringStartsWith(CommandEnum::SELECT, $findOneQuery);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage PDO Exception
     */
    public function testFindOneWithException()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository, $this->entity))
            ->command(CommandEnum::SELECT)
        ;

        $this->abstractRepository
            ->expects($this->once())
            ->method('prepareAndExecute')
            ->willThrowException(new \Exception('PDO Exception'))
        ;

        $this->abstractRepository->expects($this->once())->method('rollBack');

        $this->abstractRepository->findOne($this->queryBuilder);
    }

    public function testFind()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository, $this->entity))
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

        $resultArray = $this->abstractRepository->find($this->queryBuilder);

        $this->assertCount(\count($entityArray), $resultArray);
        foreach ($resultArray as $resultEntity) {
            $this->assertTrue($resultEntity instanceof $this->entity);
        }
    }

    public function testFindToString()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository, $this->entity))
            ->command(CommandEnum::SELECT)
        ;

        $findQuery = $this->abstractRepository->find($this->queryBuilder, true);

        $this->assertTrue(\is_string($findQuery));
        $this->assertStringStartsWith(CommandEnum::SELECT, $findQuery);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage PDO Exception
     */
    public function testFindWithException()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository, $this->entity))
            ->command(CommandEnum::SELECT)
        ;

        $this->abstractRepository
            ->expects($this->once())
            ->method('prepareAndExecute')
            ->willThrowException(new \Exception('PDO Exception'))
        ;

        $this->abstractRepository->expects($this->once())->method('rollBack');

        $this->abstractRepository->find($this->queryBuilder);
    }

    public function testUpdate()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository, $this->entity))
            ->command(CommandEnum::UPDATE)
            ->set(static::COLUMN_ONE, static::COLUMN_ONE_UPDATE_VALUE)
            ->where(\sprintf('%s = %d', static::COLUMN_AUTO_INCREMENT, static::COLUMN_AUTO_INCREMENT_VALUE))
        ;

        $this->connection->expects($this->once())->method('setSqlSafeUpdates');
        $this->connection->expects($this->once())->method('unsetSqlSafeUpdates');

        $this->abstractRepository->expects($this->once())->method('addEntityUpdateQuery')->willReturn(true);
        $this->abstractRepository->expects($this->once())->method('prepareAndExecute');
        $this->abstractRepository->expects($this->once())->method('commit');

        $entity = $this->abstractRepository->update($this->queryBuilder);

        $this->assertTrue($entity instanceof $this->entity);
    }

    public function testUpdateWithoutEntity()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository))
            ->command(CommandEnum::UPDATE)
            ->set(static::COLUMN_ONE, static::COLUMN_ONE_UPDATE_VALUE)
            ->where(\sprintf('%s = %d', static::COLUMN_AUTO_INCREMENT, static::COLUMN_AUTO_INCREMENT_VALUE))
        ;

        $this->connection->expects($this->once())->method('setSqlSafeUpdates');
        $this->connection->expects($this->once())->method('unsetSqlSafeUpdates');

        $this->abstractRepository->expects($this->once())->method('prepareAndExecute');
        $this->abstractRepository->expects($this->once())->method('commit');

        $this->assertTrue($this->abstractRepository->update($this->queryBuilder));
    }

    public function testUpdateWithoutEntityChanges()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository, $this->entity))
            ->command(CommandEnum::UPDATE)
        ;

        $this->abstractRepository->expects($this->once())->method('addEntityUpdateQuery')->willReturn(false);

        $entity = $this->abstractRepository->update($this->queryBuilder);

        $this->assertTrue($entity instanceof $this->entity);
        $this->assertEquals($this->entity, $entity);
    }

    public function testUpdateToString()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository, $this->entity))
            ->command(CommandEnum::UPDATE)
            ->set(static::COLUMN_ONE, static::COLUMN_ONE_UPDATE_VALUE)
            ->where(\sprintf('%s = %d', static::COLUMN_AUTO_INCREMENT, static::COLUMN_AUTO_INCREMENT_VALUE))
        ;

        $this->abstractRepository->expects($this->once())->method('addEntityUpdateQuery')->willReturn(true);

        $updateQuery = $this->abstractRepository->update($this->queryBuilder, true);

        $this->assertTrue(\is_string($updateQuery));
        $this->assertStringStartsWith(CommandEnum::UPDATE, $updateQuery);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage PDO Exception
     */
    public function testUpdateWithException()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository, $this->entity))
            ->command(CommandEnum::UPDATE)
            ->set(static::COLUMN_ONE, static::COLUMN_ONE_UPDATE_VALUE)
            ->where(\sprintf('%s = %d', static::COLUMN_AUTO_INCREMENT, static::COLUMN_AUTO_INCREMENT_VALUE))
        ;

        $this->connection->expects($this->once())->method('setSqlSafeUpdates');
        $this->connection->expects($this->once())->method('unsetSqlSafeUpdates');

        $this->abstractRepository->expects($this->once())->method('addEntityUpdateQuery')->willReturn(true);
        $this->abstractRepository
            ->expects($this->once())
            ->method('prepareAndExecute')
            ->willThrowException(new \Exception('PDO Exception'))
        ;

        $this->abstractRepository->update($this->queryBuilder);
    }

    public function testUpdateIgnore()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository, $this->entity))
            ->command(CommandEnum::UPDATE_IGNORE)
            ->set(static::COLUMN_ONE, static::COLUMN_ONE_UPDATE_VALUE)
            ->where(\sprintf('%s = %d', static::COLUMN_AUTO_INCREMENT, static::COLUMN_AUTO_INCREMENT_VALUE))
        ;

        $this->connection->expects($this->once())->method('setSqlSafeUpdates');
        $this->connection->expects($this->once())->method('unsetSqlSafeUpdates');

        $this->abstractRepository->expects($this->once())->method('addEntityUpdateQuery')->willReturn(true);
        $this->abstractRepository->expects($this->once())->method('prepareAndExecute');
        $this->abstractRepository->expects($this->once())->method('commit');

        $entity = $this->abstractRepository->updateIgnore($this->queryBuilder);

        $this->assertTrue($entity instanceof $this->entity);
    }

    public function testUpdateIgnoreToString()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository, $this->entity))
            ->command(CommandEnum::UPDATE_IGNORE)
            ->set(static::COLUMN_ONE, static::COLUMN_ONE_UPDATE_VALUE)
            ->where(\sprintf('%s = %d', static::COLUMN_AUTO_INCREMENT, static::COLUMN_AUTO_INCREMENT_VALUE))
        ;

        $this->abstractRepository->expects($this->once())->method('addEntityUpdateQuery')->willReturn(true);

        $updateIgnoreQuery = $this->abstractRepository->updateIgnore($this->queryBuilder, true);

        $this->assertTrue(\is_string($updateIgnoreQuery));
        $this->assertStringStartsWith(CommandEnum::UPDATE_IGNORE, $updateIgnoreQuery);
    }

    public function testDelete()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository, $this->entity))
            ->command(CommandEnum::DELETE)
            ->where(\sprintf('%s = %d', static::COLUMN_AUTO_INCREMENT, static::COLUMN_AUTO_INCREMENT_VALUE))
        ;

        $this->connection->expects($this->once())->method('setSqlSafeUpdates');
        $this->connection->expects($this->once())->method('unsetSqlSafeUpdates');

        $this->abstractRepository->expects($this->once())->method('addEntityDeleteQuery')->willReturn(true);
        $this->abstractRepository->expects($this->once())->method('prepareAndExecute');
        $this->abstractRepository->expects($this->once())->method('commit');

        $this->assertTrue($this->abstractRepository->delete($this->queryBuilder));
    }

    public function testDeleteWithoutEntity()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository))
            ->command(CommandEnum::DELETE)
            ->where(\sprintf('%s = %d', static::COLUMN_AUTO_INCREMENT, static::COLUMN_AUTO_INCREMENT_VALUE))
        ;

        $this->connection->expects($this->once())->method('setSqlSafeUpdates');
        $this->connection->expects($this->once())->method('unsetSqlSafeUpdates');

        $this->abstractRepository->expects($this->once())->method('prepareAndExecute');
        $this->abstractRepository->expects($this->once())->method('commit');

        $this->assertTrue($this->abstractRepository->delete($this->queryBuilder));
    }

    public function testDeleteWithEntityWithoutPrimaryKeys()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository, $this->entity))
            ->command(CommandEnum::DELETE)
        ;

        $this->assertFalse($this->abstractRepository->delete($this->queryBuilder));
    }

    public function testDeleteToString()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository, $this->entity))
            ->command(CommandEnum::DELETE)
            ->where(\sprintf('%s = %d', static::COLUMN_AUTO_INCREMENT, static::COLUMN_AUTO_INCREMENT_VALUE))
        ;

        $this->abstractRepository->expects($this->once())->method('addEntityDeleteQuery')->willReturn(true);

        $deleteQuery = $this->abstractRepository->delete($this->queryBuilder, true);

        $this->assertTrue(\is_string($deleteQuery));
        $this->assertStringStartsWith(CommandEnum::DELETE, $deleteQuery);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage PDO Exception
     */
    public function testDeleteWithException()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository, $this->entity))
            ->command(CommandEnum::DELETE)
        ;

        $this->connection->expects($this->once())->method('setSqlSafeUpdates');
        $this->connection->expects($this->once())->method('unsetSqlSafeUpdates');

        $this->abstractRepository->expects($this->once())->method('addEntityDeleteQuery')->willReturn(true);
        $this->abstractRepository
            ->expects($this->once())
            ->method('prepareAndExecute')
            ->willThrowException(new \Exception('PDO Exception'))
        ;

        $this->abstractRepository->delete($this->queryBuilder);
    }

    public function testCount()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository, $this->entity))
            ->command(CommandEnum::SELECT)
        ;

        $this->statement->expects($this->once())->method('fetchColumn')->with(0)->willReturn(static::RESULT_COUNT);

        $this->abstractRepository->expects($this->once())->method('prepareAndExecute')->willReturn($this->statement);

        $resultCount = $this->abstractRepository->count($this->queryBuilder);

        $this->assertEquals(static::RESULT_COUNT, $resultCount);
    }

    /**
     * @expectedException \Janisbiz\LightOrm\Dms\MySQL\Repository\RepositoryException
     * @expectedExceptionMessage
     * Command "DELETE" is not a valid command for count query! Use "SELECT" command to execute count query.
     */
    public function testCountWithWrongCommand()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository))
            ->command(CommandEnum::DELETE)
        ;

        $this->abstractRepository->count(new QueryBuilder($this->abstractRepository));
    }

    public function testCountToString()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository))
            ->command(CommandEnum::SELECT)
        ;

        $countQuery = $this->abstractRepository->count($this->queryBuilder, true);

        $this->assertTrue(\is_string($countQuery));
        $this->assertStringStartsWith(CommandEnum::SELECT, $countQuery);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage PDO Exception
     */
    public function testCountWithException()
    {
        $this->queryBuilder = (new QueryBuilder($this->abstractRepository))
            ->command(CommandEnum::SELECT)
        ;

        $this->abstractRepository
            ->expects($this->once())
            ->method('prepareAndExecute')
            ->willThrowException(new \Exception('PDO Exception'))
        ;

        $this->abstractRepository->expects($this->once())->method('rollBack');

        $this->abstractRepository->count($this->queryBuilder);
    }
}
