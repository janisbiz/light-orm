<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilder;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;
use Janisbiz\LightOrm\Dms\MySQL\Repository\AbstractRepository;
use Janisbiz\LightOrm\Entity\EntityInterface;
use Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits\AbstractTraitTestCase;

class QueryBuilderTest extends AbstractTraitTestCase
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
     * @var \ReflectionMethod[]
     */
    private $abstractRepositoryPublicMethods = [];

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
        if (empty($this->abstractRepositoryPublicMethods = $this->extractAbstractRepositoryPublicMethods())) {
            throw new \Exception('There are no AbstractRepository public methods methods!');
        }

        $this->abstractRepository = $this->getMockForAbstractClass(
            AbstractRepository::class,
            [],
            '',
            true,
            true,
            true,
            $this->abstractRepositoryPublicMethods
        );

        $this->queryBuilder = (new QueryBuilder($this->abstractRepository));
    }

    public function testQueryBuilderUsesTraits()
    {
        $this->assertObjectUsesTrait(Traits::class, $this->queryBuilder);
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
        return \array_reduce(
            [
                \array_map(
                    function ($publicMethod) {
                        return [
                            $publicMethod,
                            false
                        ];
                    },
                    $this->extractAbstractRepositoryPublicMethods()
                ),
                \array_map(
                    function ($publicMethod) {
                        return [
                            $publicMethod,
                            true
                        ];
                    },
                    $this->extractAbstractRepositoryPublicMethods()
                ),
            ]
            ,
            'array_merge',
            []
        );
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

    /**
     */
    private function extractAbstractRepositoryPublicMethods()
    {
        $abstractRepositoryReflection = new \ReflectionClass(AbstractRepository::class);
        return \array_filter(\array_map(
            function (\ReflectionMethod $abstractRepositoryPublicMethod) {
                if (2 === \count($abstractRepositoryPublicMethod->getParameters())) {
                    return $abstractRepositoryPublicMethod->getName();
                }

                return null;
            },
            $abstractRepositoryReflection->getMethods(\ReflectionMethod::IS_PUBLIC)
        ));
    }
}
