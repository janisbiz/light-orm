<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilder;
use Janisbiz\LightOrm\Dms\MySQL\Repository\AbstractRepository;
use Janisbiz\LightOrm\Entity\EntityInterface;
use PHPUnit\Framework\TestCase;

class QueryBuilderTest extends TestCase
{
    const METHOD_INSERT = 'insert';
    const METHOD_INSERT_IGNORE = 'insertIgnore';
    const METHOD_REPLACE = 'replace';
    const METHOD_FIND_ONE = 'findOne';
    const METHOD_FIND = 'find';
    const METHOD_UPDATE = 'update';
    const METHOD_UPDATE_IGNORE = 'updateIgnore';
    const METHOD_DELETE = 'delete';

    /**
     * @var AbstractRepository
     */
    private $abstractRepository;

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    public function setUp()
    {
        $this->abstractRepository = $this->getMockForAbstractClass(
            AbstractRepository::class,
            [],
            '',
            true,
            true,
            true,
            [
                self::METHOD_INSERT,
                self::METHOD_INSERT_IGNORE,
                self::METHOD_REPLACE,
                self::METHOD_FIND_ONE,
                self::METHOD_FIND,
                self::METHOD_UPDATE,
                self::METHOD_UPDATE_IGNORE,
                self::METHOD_DELETE,
            ]
        );

        $this->queryBuilder = (new QueryBuilder($this->abstractRepository));
    }

    /**
     * @dataProvider crudData
     *
     * @param string $method
     * @param bool $toString
     */
    public function testCrud($method, $toString)
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject $abstractRepository */
        $abstractRepository = $this->abstractRepository;
        $abstractRepository->expects($this->once())->method($method)->with($this->queryBuilder, $toString);
        $this->queryBuilder->$method($toString);
    }

    public function crudData()
    {
        return [
            [
                self::METHOD_INSERT,
                false,
            ],
            [
                self::METHOD_INSERT_IGNORE,
                false,
            ],
            [
                self::METHOD_REPLACE,
                false,
            ],
            [
                self::METHOD_FIND_ONE,
                false,
            ],
            [
                self::METHOD_FIND,
                false,
            ],
            [
                self::METHOD_UPDATE,
                false,
            ],
            [
                self::METHOD_UPDATE_IGNORE,
                false,
            ],
            [
                self::METHOD_DELETE,
                false,
            ],
            [
                self::METHOD_INSERT,
                true,
            ],
            [
                self::METHOD_INSERT_IGNORE,
                true,
            ],
            [
                self::METHOD_REPLACE,
                true,
            ],
            [
                self::METHOD_FIND_ONE,
                true,
            ],
            [
                self::METHOD_FIND,
                true,
            ],
            [
                self::METHOD_UPDATE,
                true,
            ],
            [
                self::METHOD_UPDATE_IGNORE,
                true,
            ],
            [
                self::METHOD_DELETE,
                true,
            ],
        ];
    }

    public function testBuildQuery()
    {
    }

    public function buildQueryData()
    {
        return [
            [

            ]
        ];
    }

    public function testGetEntity()
    {
        /** @var EntityInterface $entity */
        $entity = $this->createMock(EntityInterface::class);

        $queryBuilder = new QueryBuilder($this->abstractRepository, $entity);

        $this->assertTrue($queryBuilder->getEntity() instanceof EntityInterface);
    }

    public function testToString()
    {
    }
}
