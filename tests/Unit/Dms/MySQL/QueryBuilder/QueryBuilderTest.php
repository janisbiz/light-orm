<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder;

use Janisbiz\LightOrm\Dms\MySQL\Enum\CommandEnum;
use Janisbiz\LightOrm\Dms\MySQL\Enum\KeywordEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilder;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderInterface;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;
use Janisbiz\LightOrm\Dms\MySQL\Repository\AbstractRepository;
use Janisbiz\LightOrm\Entity\EntityInterface;
use Janisbiz\LightOrm\QueryBuilder\QueryBuilderException;
use Janisbiz\LightOrm\Repository\AbstractRepository as BaseAbstractRepository;
use Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits\AbstractTraitTestCase;
use Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits\UnionTraitTest;
use Janisbiz\LightOrm\Tests\Unit\ReflectionTrait;

class QueryBuilderTest extends AbstractTraitTestCase
{
    use ReflectionTrait;
    use QueryBuilderTrait;

    const COMMAND_INVALID = 'INVALID';

    /**
     * @var \ReflectionMethod[]
     */
    private $abstractRepositoryPublicMethods = [];

    /**
     * @var AbstractRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $abstractRepository;

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     *
     * @throws \Exception
     */
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

        $this->queryBuilder = $this->createQueryBuilder($this->abstractRepository);
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
    public function testCrud(string $method, bool $toString)
    {
        $this->abstractRepository->expects($this->once())->method($method)->with($this->queryBuilder, $toString);
        $this->queryBuilder->$method($toString);
    }

    /**
     *
     * @return array
     */
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
    public function testBuildQuery(string $command, array $methods, array $expected, array $expectedToString)
    {
        $this->addQueryPartsToQueryBuilder($command, $methods);

        $this->assertEquals(\implode(' ', $expected), $this->queryBuilder->buildQuery());
        $this->assertEquals(\implode(' ', $expectedToString), $this->queryBuilder->toString());
    }

    /**
     *
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
                    'orderBy' => [
                        'table1.column1',
                        KeywordEnum::DESC,
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
                    'ORDER BY table1.column1 DESC',
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
                    'ORDER BY table1.column1 DESC',
                    'LIMIT 1 OFFSET 2',
                ],
            ],
            [
                CommandEnum::SELECT,
                [
                    'unionAll' => function () {
                        $queryBuilder = $this->createMock(QueryBuilderInterface::class);
                        $queryBuilder
                            ->method('commandData')
                            ->willReturn(UnionTraitTest::QUERY_BUILDER_COMMAND)
                        ;
                        $queryBuilder
                            ->method('bindData')
                            ->willReturn(UnionTraitTest::QUERY_BUILDER_BIND_DATA)
                        ;
                        $queryBuilder
                            ->method('buildQuery')
                            ->willReturn(UnionTraitTest::QUERY_BUILDER_QUERY)
                        ;

                        return $queryBuilder;
                    },
                    'orderBy' => [
                        'col1',
                        KeywordEnum::DESC,
                    ],
                    'limit' => 1,
                    'offset' => 2,
                ],
                [
                    '(SELECT col1, col2, col3 FROM table1 WHERE table1.col1 = :col1 AND table1.col2 IS NOT NULL)',
                    'UNION ALL',
                    '(SELECT col1, col2, col3 FROM table1 WHERE table1.col1 = :col1 AND table1.col2 IS NOT NULL)',
                    'UNION ALL',
                    '(SELECT col1, col2, col3 FROM table1 WHERE table1.col1 = :col1 AND table1.col2 IS NOT NULL)',
                    'ORDER BY col1 DESC',
                    'LIMIT 1 OFFSET 2',
                ],
                [
                    '(SELECT col1, col2, col3 FROM table1 WHERE table1.col1 = \'val1\' AND table1.col2 IS NOT NULL)',
                    'UNION ALL',
                    '(SELECT col1, col2, col3 FROM table1 WHERE table1.col1 = \'val1\' AND table1.col2 IS NOT NULL)',
                    'UNION ALL',
                    '(SELECT col1, col2, col3 FROM table1 WHERE table1.col1 = \'val1\' AND table1.col2 IS NOT NULL)',
                    'ORDER BY col1 DESC',
                    'LIMIT 1 OFFSET 2',
                ],
            ],
            [
                CommandEnum::UPDATE,
                [
                    'table' => 'table1',
                    'set' => [
                        'table1.column1',
                        'table1Column1Value',
                    ],
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
                    'limit' => 1,
                    'offset' => 2,
                ],
                [
                    'UPDATE',
                    'table1',
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
                    'SET table1.column1 = :Table1_Column1_Update',
                    'WHERE table1.column1 = :table1Column1BindValue',
                    'AND table1.column2 IN (:0_table1column2_In, :1_table1column2_In)',
                    'AND table1.column3 NOT IN (:0_table1column3_NotIn, :1_table1column3_NotIn)',
                    'LIMIT 1 OFFSET 2'
                ],
                [
                    'UPDATE',
                    'table1',
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
                    'SET table1.column1 = \'table1Column1Value\'',
                    'WHERE table1.column1 = \'table1Column1BindValue\'',
                    'AND table1.column2 IN (\'table1Column2BindValue1\', \'table1Column2BindValue2\')',
                    'AND table1.column3 NOT IN (\'table1Column3BindValue1\', \'table1Column3BindValue2\')',
                    'LIMIT 1 OFFSET 2',
                ],
            ],
            [
                CommandEnum::DELETE,
                [
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
                    'limit' => 1,
                    'offset' => 2,
                ],
                [
                    'DELETE',
                    'table1 FROM table1',
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
                    'LIMIT 1 OFFSET 2',
                ],
                [
                    'DELETE',
                    'table1 FROM table1',
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
                    'LIMIT 1 OFFSET 2',
                ],
            ],
        ];
        /** phpcs:enable */
    }

    /**
     * @dataProvider buildQueryWithMissingQueryPartsProvideData
     *
     * @param string $command
     * @param string[] $methods
     * @param string $expectedException
     * @param string $expectedExceptionMessage
     */
    public function testBuildQueryWithMissingQueryPartsProvided(
        string $command,
        array $methods,
        string $expectedException,
        string $expectedExceptionMessage
    ) {
        $this->addQueryPartsToQueryBuilder($command, $methods);

        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->queryBuilder->buildQuery();
    }

    /**
     * @return array
     */
    public function buildQueryWithMissingQueryPartsProvideData()
    {
        return [
            [
                '',
                [],
                QueryBuilderException::class,
                'Could not build query, as there is no command provided!',
            ],
            [
                CommandEnum::UPDATE,
                [],
                QueryBuilderException::class,
                'Cannot perform UPDATE action without SET condition!',
            ],
            [
                CommandEnum::UPDATE,
                [
                    'set' => [
                        'table1.column1',
                        'table1Column1Value',
                    ],
                ],
                QueryBuilderException::class,
                'Cannot perform UPDATE action without WHERE condition!',
            ],
            [
                CommandEnum::UPDATE_IGNORE,
                [],
                QueryBuilderException::class,
                'Cannot perform UPDATE action without SET condition!',
            ],
            [
                CommandEnum::UPDATE_IGNORE,
                [
                    'set' => [
                        'table1.column1',
                        'table1Column1Value',
                    ],
                ],
                QueryBuilderException::class,
                'Cannot perform UPDATE action without WHERE condition!',
            ],
            [
                CommandEnum::DELETE,
                [],
                QueryBuilderException::class,
                'Cannot perform DELETE action without WHERE condition!',
            ],
            [
                CommandEnum::DELETE,
                [],
                QueryBuilderException::class,
                'Cannot perform DELETE action without WHERE condition!',
            ],
            [
                static::COMMAND_INVALID,
                [],
                QueryBuilderException::class,
                \sprintf('Could not build query, as there is no valid(%s) command provided!', static::COMMAND_INVALID),
            ],
        ];
    }

    public function testGetEntity()
    {
        /** @var EntityInterface $entity */
        $entity = $this->createMock(EntityInterface::class);

        $queryBuilder = $this->createQueryBuilder($this->abstractRepository, $entity);

        $this->assertTrue($queryBuilder->getEntity() instanceof EntityInterface);
    }

    /**
     * @param string $command
     * @param string[] $methods
     *
     * @return $this
     */
    private function addQueryPartsToQueryBuilder(string $command, array $methods)
    {
        $this->queryBuilder->command($command);

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
                case 'set':
                case 'orderBy':
                    $this->queryBuilder->$method($value[0], $value[1]);

                    break;

                case 'unionAll':
                    $queryBuilder = $value();

                    $unionAllQueries = [
                        $queryBuilder,
                        $queryBuilder,
                        $queryBuilder,
                    ];

                    foreach ($unionAllQueries as $unionAllQuery) {
                        $this->queryBuilder->unionAll($unionAllQuery);
                    }

                    break;
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    private function extractAbstractRepositoryPublicMethods()
    {
        $abstractRepositoryReflection = new \ReflectionClass(AbstractRepository::class);

        return \array_filter(\array_diff(
            \array_map(
                function (\ReflectionMethod $abstractRepositoryMethod) {
                    if (2 === $abstractRepositoryMethod->getNumberOfParameters()
                        && 'queryBuilder' === $abstractRepositoryMethod->getParameters()[0]->getName()
                        && 'toString' === $abstractRepositoryMethod->getParameters()[1]->getName()
                    ) {
                        return $abstractRepositoryMethod->getName();
                    }

                    return null;
                },
                $abstractRepositoryReflection->getMethods(\ReflectionMethod::IS_PROTECTED)
            ),
            \array_reduce(
                \array_map(
                    function (array $publicMethods) {
                        return \array_map(
                            function (\ReflectionMethod $publicMethod) {
                                return $publicMethod->getName();
                            },
                            $publicMethods
                        );
                    },
                    [
                        (new \ReflectionClass(BaseAbstractRepository::class))
                            ->getMethods(\ReflectionMethod::IS_PROTECTED | \ReflectionMethod::IS_ABSTRACT),
                    ]
                ),
                'array_merge',
                []
            )
        ));
    }
}
