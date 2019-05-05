<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder;

use Janisbiz\LightOrm\Dms\MySQL\Enum\CommandEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilder;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;
use Janisbiz\LightOrm\Dms\MySQL\Repository\AbstractRepository;
use Janisbiz\LightOrm\Entity\EntityInterface;
use Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits\AbstractTraitTestCase;

class QueryBuilderTest extends AbstractTraitTestCase
{
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
            ],
            'array_merge',
            []
        );
    }

    /**
     * @dataProvider buildQueryData
     *
     * @param string $command
     * @param array $methods
     * @param array $expected
     * @param array $expectedToString
     */
    public function testBuildQuery($command, $methods, $expected, $expectedToString)
    {
        foreach ($methods as $method => $value) {
            switch ($method) {
                case 'column':
                case 'table':
                case 'groupBy':
                case 'limit':
                case 'offset':
                    $this->queryBuilder->$method($value);
                    break;

                case 'innerJoin':
                case 'leftJoin':
                case 'rightJoin':
                case 'crossJoin':
                case 'fullOuterJoin':
                    $this->queryBuilder->$method($value[0], $value[1], !empty($value[2]) ? $value[2] : []);
                    break;

                case 'innerJoinAs':
                case 'leftJoinAs':
                case 'rightJoinAs':
                case 'crossJoinAs':
                case 'fullOuterJoinAs':
                    $this->queryBuilder->$method($value[0], $value[1], $value[2], !empty($value[3]) ? $value[3] : []);
                    break;

                case 'where':
                case 'whereIn':
                case 'whereNotIn':
                case 'having':
                case 'value':
                case 'onDuplicateKeyUpdate':
                    $this->queryBuilder->$method($value[0], $value[1]);
                    break;
            }
        }

        $this->queryBuilder->command($command);

        dump($this->queryBuilder->buildQuery());
        dump($this->queryBuilder->toString());

        $this->assertEquals(\implode(' ', $expected), $this->queryBuilder->buildQuery());
        $this->assertEquals(\implode(' ', $expectedToString), $this->queryBuilder->toString());
    }

    /**
     * @return array
     */
    public function buildQueryData()
    {
        /** phpcs:disable */
        return [
            [
                CommandEnum::INSERT_INTO,
                [
                    'table' => 'table1',
                    'value' => [
                        'table1.column1',
                        'table1Column1Value',
                    ],
                    'onDuplicateKeyUpdate' => [
                        'table1.column1',
                        'table1Column1Value2',
                    ],
                ],
                [
                    'INSERT INTO',
                    'table1',
                    '(table1.column1) VALUES (:Table1_Column1_Value)',
                    'ON DUPLICATE KEY UPDATE table1.column1 = :Table1_Column1_OnDuplicateKeyUpdate',
                ],
                [
                    'INSERT INTO',
                    'table1',
                    '(table1.column1) VALUES (\'table1Column1Value\')',
                    'ON DUPLICATE KEY UPDATE table1.column1 = \'table1Column1Value2\'',
                ],
            ],
            [
                CommandEnum::SELECT,
                [
                    'column' => '*',
                    'table' => 'table1',
                    'innerJoin' => [
                        'table2',
                        'table2.id = table1.table2_id AND table2.column1 = :bindValue2',
                        [
                            'bindValue2' => 2,
                        ],
                    ],
                    'leftJoin' => [
                        'table3',
                        'table3.id = table1.table3_id AND table3.column1 = :bindValue3',
                        [
                            'bindValue3' => 3,
                        ],
                    ],
                    'rightJoin' => [
                        'table4',
                        'table4.id = table1.table4_id AND table4.column1 = :bindValue4',
                        [
                            'bindValue4' => 4,
                        ],
                    ],
                    'crossJoin' => [
                        'table5',
                        'table5.id = table1.table5_id AND table5.column1 = :bindValue5',
                        [
                            'bindValue5' => 5,
                        ],
                    ],
                    'fullOuterJoin' => [
                        'table6',
                        'table6.id = table1.table6_id AND table6.column1 = :bindValue6',
                        [
                            'bindValue6' => 6,
                        ],
                    ],
                    'innerJoinAs' => [
                        'table7',
                        'table7_alias',
                        'table7.id = table1.table7_id AND table7.column1 = :bindValue7',
                        [
                            'bindValue7' => 7,
                        ],
                    ],
                    'leftJoinAs' => [
                        'table8',
                        'table8_alias',
                        'table8.id = table1.table8_id AND table8.column1 = :bindValue8',
                        [
                            'bindValue8' => 8,
                        ],
                    ],
                    'rightJoinAs' => [
                        'table9',
                        'table9_alias',
                        'table9.id = table1.table9_id AND table9.column1 = :bindValue9',
                        [
                            'bindValue9' => 9,
                        ],
                    ],
                    'crossJoinAs' => [
                        'table10',
                        'table10_alias',
                        'table10.id = table1.table10_id AND table10.column1 = :bindValue10',
                        [
                            'bindValue10' => 10,
                        ],
                    ],
                    'fullOuterJoinAs' => [
                        'table11',
                        'table11_alias',
                        'table11.id = table1.table11_id AND table11.column1 = :bindValue11',
                        [
                            'bindValue11' => 11,
                        ],
                    ],
                    'where' => [
                        'table1.column1 = :table1Column1BindValue',
                        [
                            'table1Column1BindValue' => 'table1Column1BindValue'
                        ]
                    ],
                    'whereIn' => [
                        'table1.column2',
                        [
                            'table1Column2BindValue1',
                            'table1Column2BindValue2',
                        ]
                    ],
                    'whereNotIn' => [
                        'table1.column3',
                        [
                            'table1Column3BindValue1',
                            'table1Column3BindValue2',
                        ]
                    ],
                    'groupBy' => 'table1.column1',
                    'having' => [
                        'table1.column1 = table2.column2 AND table1.column2 = :havingTable1Column2',
                        [
                            'havingTable1Column2' => 1.2,
                        ]
                    ],
                    'limit' => 1,
                    'offset' => 2,
                ],
                [
                    'SELECT',
                    '*',
                    'FROM table1',
                    'INNER JOIN table2 ON (table2.id = table1.table2_id AND table2.column1 = :bindValue2)',
                    'LEFT JOIN table3 ON (table3.id = table1.table3_id AND table3.column1 = :bindValue3)',
                    'RIGHT JOIN table4 ON (table4.id = table1.table4_id AND table4.column1 = :bindValue4)',
                    'CROSS JOIN table5 ON (table5.id = table1.table5_id AND table5.column1 = :bindValue5)',
                    'FULL OUTER JOIN table6 ON (table6.id = table1.table6_id AND table6.column1 = :bindValue6)',
                    'INNER JOIN table7 AS table7_alias ON (table7.id = table1.table7_id AND table7.column1 = :bindValue7)',
                    'LEFT JOIN table8 AS table8_alias ON (table8.id = table1.table8_id AND table8.column1 = :bindValue8)',
                    'RIGHT JOIN table9 AS table9_alias ON (table9.id = table1.table9_id AND table9.column1 = :bindValue9)',
                    'CROSS JOIN table10 AS table10_alias ON (table10.id = table1.table10_id AND table10.column1 = :bindValue10)',
                    'FULL OUTER JOIN table11 AS table11_alias ON (table11.id = table1.table11_id AND table11.column1 = :bindValue11)',
                    'WHERE table1.column1 = :table1Column1BindValue',
                    'AND table1.column2 IN (:0_table1column2_In, :1_table1column2_In)',
                    'AND table1.column3 NOT IN (:0_table1column3_NotIn, :1_table1column3_NotIn)',
                    'GROUP BY table1.column1',
                    'HAVING table1.column1 = table2.column2 AND table1.column2 = :havingTable1Column2',
                    'LIMIT 1 OFFSET 2',
                ],
                [
                    'SELECT',
                    '*',
                    'FROM table1',
                    'INNER JOIN table2 ON (table2.id = table1.table2_id AND table2.column1 = 2)',
                    'LEFT JOIN table3 ON (table3.id = table1.table3_id AND table3.column1 = 3)',
                    'RIGHT JOIN table4 ON (table4.id = table1.table4_id AND table4.column1 = 4)',
                    'CROSS JOIN table5 ON (table5.id = table1.table5_id AND table5.column1 = 5)',
                    'FULL OUTER JOIN table6 ON (table6.id = table1.table6_id AND table6.column1 = 6)',
                    'INNER JOIN table7 AS table7_alias ON (table7.id = table1.table7_id AND table7.column1 = 7)',
                    'LEFT JOIN table8 AS table8_alias ON (table8.id = table1.table8_id AND table8.column1 = 8)',
                    'RIGHT JOIN table9 AS table9_alias ON (table9.id = table1.table9_id AND table9.column1 = 9)',
                    'CROSS JOIN table10 AS table10_alias ON (table10.id = table1.table10_id AND table10.column1 = 10)',
                    'FULL OUTER JOIN table11 AS table11_alias ON (table11.id = table1.table11_id AND table11.column1 = 11)',
                    'WHERE table1.column1 = \'table1Column1BindValue\'',
                    'AND table1.column2 IN (\'table1Column2BindValue1\', \'table1Column2BindValue2\')',
                    'AND table1.column3 NOT IN (\'table1Column3BindValue1\', \'table1Column3BindValue2\')',
                    'GROUP BY table1.column1',
                    'HAVING table1.column1 = table2.column2 AND table1.column2 = 1.2',
                    'LIMIT 1 OFFSET 2',
                ],
            ],
        ];
        /** phpcs:enable */
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
