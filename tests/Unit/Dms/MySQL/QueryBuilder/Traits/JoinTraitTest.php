<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\JoinEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\BindTrait;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\JoinTrait;

class JoinTraitTest extends AbstractTraitTestCase
{
    use BindTrait;
    use JoinTrait;

    const JOIN_INVALID_JOIN = 'INVALID';

    const JOIN_EMPTY_TABLE = null;
    const JOIN_EMPTY_ON_CONDITION = null;
    const JOIN_EMPTY_ALIAS = null;

    const JOIN_DEFAULT_JOIN = JoinEnum::INNER_JOIN;
    const JOIN_DEFAULT_TABLE = 'table1';
    const JOIN_DEFAULT_ON_CONDITION = 'table1.column1 = table2.column2 AND table1.column2 = :column2_ValueBind';
    const JOIN_DEFAULT = [
        self::JOIN_DEFAULT_JOIN . ' ' . self::JOIN_DEFAULT_TABLE . ' ON (' . self::JOIN_DEFAULT_ON_CONDITION . ')',
    ];
    const JOIN_BIND_DEFAULT = [
        'column2_ValueBind' => 'column2_value',
    ];

    const JOIN_TABLE = 'table0';
    const JOIN_TABLE_ALIAS = 'table0_alias';
    const JOIN_ON_CONDITION = 'table0.column0 = table1.column1 AND table1.column3 = :column3_ValueBind';
    const JOIN_BIND = [
        'column3_ValueBind' => 'column3_value',
    ];


    public function setUp()
    {
        $this->bind = self::JOIN_BIND_DEFAULT;
        $this->join = self::JOIN_DEFAULT;
    }

    /**
     * @dataProvider joinData
     *
     * @param string $join
     * @param string $tableName
     * @param string $onCondition
     * @param array $bind
     */
    public function testJoin($join, $tableName, $onCondition, array $bind)
    {
        $object = $this->join($join, $tableName, $onCondition, $bind);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(JoinTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                self::JOIN_DEFAULT,
                [
                    \sprintf('%s %s ON (%s)', $join, $tableName, $onCondition),
                ]
            ),
            $this->join
        );
        $this->assertEquals(
            \array_merge(
                self::JOIN_BIND_DEFAULT,
                $bind
            ),
            $this->bind
        );
    }

    /**
     * @dataProvider joinData
     *
     * @param string $join
     * @param string $tableName
     * @param string $onCondition
     * @param array $bind
     */
    public function testBuildJoinQueryPart($join, $tableName, $onCondition, array $bind)
    {
        $this->join($join, $tableName, $onCondition, $bind);

        $this->assertEquals(\implode(' ', $this->join), $this->buildJoinQueryPart());
    }

    public function testBuildJoinQueryPartWhenEmpty()
    {
        $this->join = [];

        $this->assertEquals(null, $this->buildJoinQueryPart());
    }

    /**
     * @return array
     */
    public function joinData()
    {
        return [
            [
                JoinEnum::LEFT_JOIN,
                'table3',
                'table3.id = table1.table3_id',
                []
            ],
            [
                JoinEnum::LEFT_JOIN,
                'table4',
                'table4.id = table1.table4_id AND table4.column = :table4_column',
                [
                    'table4_column' => 'value',
                ]
            ],
            [
                JoinEnum::INNER_JOIN,
                'table5',
                'table5.id = table1.table5_id',
                []
            ],
            [
                JoinEnum::INNER_JOIN,
                'table6',
                'table6.id = table1.table6_id AND table6.column = :table6_column',
                [
                    'table6_column' => 'value',
                ]
            ],
            [
                JoinEnum::FULL_OUTER_JOIN,
                'table7',
                'table7.id = table1.table7_id',
                []
            ],
            [
                JoinEnum::FULL_OUTER_JOIN,
                'table8',
                'table8.id = table1.table8_id AND table8.column = :table8_column',
                [
                    'table8_column' => 'value',
                ]
            ],
            [
                JoinEnum::RIGHT_JOIN,
                'table9',
                'table9.id = table1.table9_id',
                []
            ],
            [
                JoinEnum::RIGHT_JOIN,
                'table10',
                'table10.id = table1.table10_id AND table10.column = :table10_column',
                [
                    'table10_column' => 'value',
                ]
            ],
            [
                JoinEnum::CROSS_JOIN,
                'table11',
                'table11.id = table1.table11_id',
                []
            ],
            [
                JoinEnum::CROSS_JOIN,
                'table12',
                'table12.id = table1.table12_id AND table12.column = :table12_column',
                [
                    'table12_column' => 'value',
                ]
            ],
        ];
    }

    public function testJoinWithSameJoin()
    {
        $object = $this->join(
            self::JOIN_DEFAULT_JOIN,
            self::JOIN_DEFAULT_TABLE,
            self::JOIN_DEFAULT_ON_CONDITION,
            self::JOIN_BIND_DEFAULT
        );
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(JoinTrait::class, $object);
        $this->assertEquals(self::JOIN_DEFAULT, $this->join);
        $this->assertEquals(self::JOIN_BIND_DEFAULT, $this->bind);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage $join "INVALID" is not a valid join type
     */
    public function testJoinWithInvalidJoin()
    {
        $this->join(
            self::JOIN_INVALID_JOIN,
            self::JOIN_DEFAULT_TABLE,
            self::JOIN_DEFAULT_ON_CONDITION,
            self::JOIN_BIND_DEFAULT
        );
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage You must pass $table name to join method!
     */
    public function testJoinWithEmptyTable()
    {
        $this->join(
            self::JOIN_DEFAULT_JOIN,
            self::JOIN_EMPTY_TABLE,
            self::JOIN_DEFAULT_ON_CONDITION,
            self::JOIN_BIND_DEFAULT
        );
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage You must pass $onCondition name to join method!
     */
    public function testJoinWithEmptyOnCondition()
    {
        $this->join(
            self::JOIN_DEFAULT_JOIN,
            self::JOIN_DEFAULT_TABLE,
            self::JOIN_EMPTY_ON_CONDITION,
            self::JOIN_BIND_DEFAULT
        );
    }

    /**
     * @dataProvider joinAsData
     *
     * @param string $join
     * @param string $tableName
     * @param string $alias
     * @param string $onCondition
     * @param array $bind
     */
    public function testJoinAs($join, $tableName, $alias, $onCondition, array $bind)
    {
        $object = $this->joinAs($join, $tableName, $alias, $onCondition, $bind);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(JoinTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                self::JOIN_DEFAULT,
                [
                    \sprintf('%s %s AS %s ON (%s)', $join, $tableName, $alias, $onCondition),
                ]
            ),
            $this->join
        );
        $this->assertEquals(
            \array_merge(
                self::JOIN_BIND_DEFAULT,
                $bind
            ),
            $this->bind
        );
    }

    /**
     * @return array
     */
    public function joinAsData()
    {
        return [
            [
                JoinEnum::LEFT_JOIN,
                'table3',
                'alias_table3',
                'table3.id = table1.table3_id',
                []
            ],
            [
                JoinEnum::LEFT_JOIN,
                'table4',
                'alias_table4',
                'table4.id = table1.table4_id AND table4.column = :table4_column',
                [
                    'table4_column' => 'value',
                ]
            ],
            [
                JoinEnum::INNER_JOIN,
                'table5',
                'alias_table5',
                'table5.id = table1.table5_id',
                []
            ],
            [
                JoinEnum::INNER_JOIN,
                'table6',
                'alias_table6',
                'table6.id = table1.table6_id AND table6.column = :table6_column',
                [
                    'table6_column' => 'value',
                ]
            ],
            [
                JoinEnum::FULL_OUTER_JOIN,
                'table7',
                'alias_table7',
                'table7.id = table1.table7_id',
                []
            ],
            [
                JoinEnum::FULL_OUTER_JOIN,
                'table8',
                'alias_table8',
                'table8.id = table1.table8_id AND table8.column = :table8_column',
                [
                    'table8_column' => 'value',
                ]
            ],
            [
                JoinEnum::RIGHT_JOIN,
                'table9',
                'alias_table9',
                'table9.id = table1.table9_id',
                []
            ],
            [
                JoinEnum::RIGHT_JOIN,
                'table10',
                'alias_table10',
                'table10.id = table1.table10_id AND table10.column = :table10_column',
                [
                    'table10_column' => 'value',
                ]
            ],
            [
                JoinEnum::CROSS_JOIN,
                'table11',
                'alias_table11',
                'table11.id = table1.table11_id',
                []
            ],
            [
                JoinEnum::CROSS_JOIN,
                'table12',
                'alias_table12',
                'table12.id = table1.table12_id AND table12.column = :table12_column',
                [
                    'table12_column' => 'value',
                ]
            ],
        ];
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage You must pass $alias name to join method!
     */
    public function testJoinAsWithEmptyAlias()
    {
        $this->joinAs(
            self::JOIN_DEFAULT_JOIN,
            self::JOIN_DEFAULT_TABLE,
            self::JOIN_EMPTY_ALIAS,
            self::JOIN_EMPTY_ON_CONDITION,
            self::JOIN_BIND_DEFAULT
        );
    }

    public function testInnerJoin()
    {
        $object = $this->innerJoin(self::JOIN_TABLE, self::JOIN_ON_CONDITION, self::JOIN_BIND);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(JoinTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                self::JOIN_DEFAULT,
                [
                    \sprintf('%s %s ON (%s)', JoinEnum::INNER_JOIN, self::JOIN_TABLE, self::JOIN_ON_CONDITION),
                ]
            ),
            $this->join
        );
        $this->assertEquals(
            \array_merge(
                self::JOIN_BIND_DEFAULT,
                self::JOIN_BIND
            ),
            $this->bind
        );
    }

    public function testInnerJoinAs()
    {
        $object = $this->innerJoinAs(
            self::JOIN_TABLE,
            self::JOIN_TABLE_ALIAS,
            self::JOIN_ON_CONDITION,
            self::JOIN_BIND
        );
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(JoinTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                self::JOIN_DEFAULT,
                [
                    \sprintf(
                        '%s %s AS %s ON (%s)',
                        JoinEnum::INNER_JOIN,
                        self::JOIN_TABLE,
                        self::JOIN_TABLE_ALIAS,
                        self::JOIN_ON_CONDITION
                    ),
                ]
            ),
            $this->join
        );
        $this->assertEquals(
            \array_merge(
                self::JOIN_BIND_DEFAULT,
                self::JOIN_BIND
            ),
            $this->bind
        );
    }

    public function testLeftJoin()
    {
        $object = $this->leftJoin(self::JOIN_TABLE, self::JOIN_ON_CONDITION, self::JOIN_BIND);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(JoinTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                self::JOIN_DEFAULT,
                [
                    \sprintf('%s %s ON (%s)', JoinEnum::LEFT_JOIN, self::JOIN_TABLE, self::JOIN_ON_CONDITION),
                ]
            ),
            $this->join
        );
        $this->assertEquals(
            \array_merge(
                self::JOIN_BIND_DEFAULT,
                self::JOIN_BIND
            ),
            $this->bind
        );
    }

    public function testLeftJoinAs()
    {
        $object = $this->leftJoinAs(
            self::JOIN_TABLE,
            self::JOIN_TABLE_ALIAS,
            self::JOIN_ON_CONDITION,
            self::JOIN_BIND
        );
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(JoinTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                self::JOIN_DEFAULT,
                [
                    \sprintf(
                        '%s %s AS %s ON (%s)',
                        JoinEnum::LEFT_JOIN,
                        self::JOIN_TABLE,
                        self::JOIN_TABLE_ALIAS,
                        self::JOIN_ON_CONDITION
                    ),
                ]
            ),
            $this->join
        );
        $this->assertEquals(
            \array_merge(
                self::JOIN_BIND_DEFAULT,
                self::JOIN_BIND
            ),
            $this->bind
        );
    }

    public function testRightJoin()
    {
        $object = $this->rightJoin(self::JOIN_TABLE, self::JOIN_ON_CONDITION, self::JOIN_BIND);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(JoinTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                self::JOIN_DEFAULT,
                [
                    \sprintf('%s %s ON (%s)', JoinEnum::RIGHT_JOIN, self::JOIN_TABLE, self::JOIN_ON_CONDITION),
                ]
            ),
            $this->join
        );
        $this->assertEquals(
            \array_merge(
                self::JOIN_BIND_DEFAULT,
                self::JOIN_BIND
            ),
            $this->bind
        );
    }

    public function testRightJoinAs()
    {
        $object = $this->rightJoinAs(
            self::JOIN_TABLE,
            self::JOIN_TABLE_ALIAS,
            self::JOIN_ON_CONDITION,
            self::JOIN_BIND
        );
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(JoinTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                self::JOIN_DEFAULT,
                [
                    \sprintf(
                        '%s %s AS %s ON (%s)',
                        JoinEnum::RIGHT_JOIN,
                        self::JOIN_TABLE,
                        self::JOIN_TABLE_ALIAS,
                        self::JOIN_ON_CONDITION
                    ),
                ]
            ),
            $this->join
        );
        $this->assertEquals(
            \array_merge(
                self::JOIN_BIND_DEFAULT,
                self::JOIN_BIND
            ),
            $this->bind
        );
    }

    public function testCrossJoin()
    {
        $object = $this->crossJoin(self::JOIN_TABLE, self::JOIN_ON_CONDITION, self::JOIN_BIND);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(JoinTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                self::JOIN_DEFAULT,
                [
                    \sprintf('%s %s ON (%s)', JoinEnum::CROSS_JOIN, self::JOIN_TABLE, self::JOIN_ON_CONDITION),
                ]
            ),
            $this->join
        );
        $this->assertEquals(
            \array_merge(
                self::JOIN_BIND_DEFAULT,
                self::JOIN_BIND
            ),
            $this->bind
        );
    }

    public function testCrossJoinAs()
    {
        $object = $this->crossJoinAs(
            self::JOIN_TABLE,
            self::JOIN_TABLE_ALIAS,
            self::JOIN_ON_CONDITION,
            self::JOIN_BIND
        );
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(JoinTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                self::JOIN_DEFAULT,
                [
                    \sprintf(
                        '%s %s AS %s ON (%s)',
                        JoinEnum::CROSS_JOIN,
                        self::JOIN_TABLE,
                        self::JOIN_TABLE_ALIAS,
                        self::JOIN_ON_CONDITION
                    ),
                ]
            ),
            $this->join
        );
        $this->assertEquals(
            \array_merge(
                self::JOIN_BIND_DEFAULT,
                self::JOIN_BIND
            ),
            $this->bind
        );
    }

    public function testFullOuterJoin()
    {
        $object = $this->fullOuterJoin(self::JOIN_TABLE, self::JOIN_ON_CONDITION, self::JOIN_BIND);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(JoinTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                self::JOIN_DEFAULT,
                [
                    \sprintf('%s %s ON (%s)', JoinEnum::FULL_OUTER_JOIN, self::JOIN_TABLE, self::JOIN_ON_CONDITION),
                ]
            ),
            $this->join
        );
        $this->assertEquals(
            \array_merge(
                self::JOIN_BIND_DEFAULT,
                self::JOIN_BIND
            ),
            $this->bind
        );
    }

    public function testFullOuterJoinAs()
    {
        $object = $this->fullOuterJoinAs(
            self::JOIN_TABLE,
            self::JOIN_TABLE_ALIAS,
            self::JOIN_ON_CONDITION,
            self::JOIN_BIND
        );
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(JoinTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                self::JOIN_DEFAULT,
                [
                    \sprintf(
                        '%s %s AS %s ON (%s)',
                        JoinEnum::FULL_OUTER_JOIN,
                        self::JOIN_TABLE,
                        self::JOIN_TABLE_ALIAS,
                        self::JOIN_ON_CONDITION
                    ),
                ]
            ),
            $this->join
        );
        $this->assertEquals(
            \array_merge(
                self::JOIN_BIND_DEFAULT,
                self::JOIN_BIND
            ),
            $this->bind
        );
    }
}
