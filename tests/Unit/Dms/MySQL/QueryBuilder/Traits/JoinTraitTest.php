<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\JoinEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\BindTrait;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\JoinTrait;

class JoinTraitTest extends AbstractTraitTestCase
{
    const JOIN_INVALID_JOIN = 'INVALID';

    const JOIN_EMPTY_TABLE = '';
    const JOIN_EMPTY_ON_CONDITION = '';
    const JOIN_EMPTY_ALIAS = '';

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

    /**
     * @var JoinTrait|BindTrait
     */
    private $joinTraitClass;

    public function setUp()
    {
        $this->joinTraitClass = new class (JoinTraitTest::JOIN_BIND_DEFAULT, JoinTraitTest::JOIN_DEFAULT)
        {
            use BindTrait;
            use JoinTrait;

            /**
             * @param array $bindDataDefault
             * @param array $joinDataDefault
             */
            public function __construct(array $bindDataDefault, array $joinDataDefault)
            {
                $this->bind = $bindDataDefault;
                $this->join = $joinDataDefault;
            }

            /**
             * @return array
             */
            public function joinData(): array
            {
                return $this->join;
            }

            public function clearJoinData()
            {
                $this->join = [];
            }

            /**
             * @return null|string
             */
            public function buildJoinQueryPartPublic(): ?string
            {
                return $this->buildJoinQueryPart();
            }
        };
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
        $object = $this->joinTraitClass->join($join, $tableName, $onCondition, $bind);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(JoinTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                static::JOIN_DEFAULT,
                [
                    \sprintf('%s %s ON (%s)', $join, $tableName, $onCondition),
                ]
            ),
            $this->joinTraitClass->joinData()
        );
        $this->assertEquals(
            \array_merge(
                static::JOIN_BIND_DEFAULT,
                $bind
            ),
            $this->joinTraitClass->bindData()
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
        $this->joinTraitClass->join($join, $tableName, $onCondition, $bind);

        $this->assertEquals(
            \implode(' ', $this->joinTraitClass->joinData()),
            $this->joinTraitClass->buildJoinQueryPartPublic()
        );
    }

    public function testBuildJoinQueryPartWhenEmpty()
    {
        $this->joinTraitClass->clearJoinData();

        $this->assertEquals(null, $this->joinTraitClass->buildJoinQueryPartPublic());
    }

    /**
     *
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
        $object = $this->joinTraitClass->join(
            static::JOIN_DEFAULT_JOIN,
            static::JOIN_DEFAULT_TABLE,
            static::JOIN_DEFAULT_ON_CONDITION,
            static::JOIN_BIND_DEFAULT
        );
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(JoinTrait::class, $object);
        $this->assertEquals(static::JOIN_DEFAULT, $this->joinTraitClass->joinData());
        $this->assertEquals(static::JOIN_BIND_DEFAULT, $this->joinTraitClass->bindData());
    }

    public function testJoinWithInvalidJoin()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('$join "INVALID" is not a valid join type');

        $this->joinTraitClass->join(
            static::JOIN_INVALID_JOIN,
            static::JOIN_DEFAULT_TABLE,
            static::JOIN_DEFAULT_ON_CONDITION,
            static::JOIN_BIND_DEFAULT
        );
    }

    public function testJoinWithEmptyTable()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('You must pass $table name to join method!');

        $this->joinTraitClass->join(
            static::JOIN_DEFAULT_JOIN,
            static::JOIN_EMPTY_TABLE,
            static::JOIN_DEFAULT_ON_CONDITION,
            static::JOIN_BIND_DEFAULT
        );
    }

    public function testJoinWithEmptyOnCondition()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('You must pass $onCondition name to join method!');

        $this->joinTraitClass->join(
            static::JOIN_DEFAULT_JOIN,
            static::JOIN_DEFAULT_TABLE,
            static::JOIN_EMPTY_ON_CONDITION,
            static::JOIN_BIND_DEFAULT
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
        $object = $this->joinTraitClass->joinAs($join, $tableName, $alias, $onCondition, $bind);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(JoinTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                static::JOIN_DEFAULT,
                [
                    \sprintf('%s %s AS %s ON (%s)', $join, $tableName, $alias, $onCondition),
                ]
            ),
            $this->joinTraitClass->joinData()
        );
        $this->assertEquals(
            \array_merge(
                static::JOIN_BIND_DEFAULT,
                $bind
            ),
            $this->joinTraitClass->bindData()
        );
    }

    /**
     *
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

    public function testJoinAsWithEmptyAlias()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('You must pass $alias name to join method!');

        $this->joinTraitClass->joinAs(
            static::JOIN_DEFAULT_JOIN,
            static::JOIN_DEFAULT_TABLE,
            static::JOIN_EMPTY_ALIAS,
            static::JOIN_EMPTY_ON_CONDITION,
            static::JOIN_BIND_DEFAULT
        );
    }

    public function testInnerJoin()
    {
        $object = $this->joinTraitClass->innerJoin(static::JOIN_TABLE, static::JOIN_ON_CONDITION, static::JOIN_BIND);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(JoinTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                static::JOIN_DEFAULT,
                [
                    \sprintf('%s %s ON (%s)', JoinEnum::INNER_JOIN, static::JOIN_TABLE, static::JOIN_ON_CONDITION),
                ]
            ),
            $this->joinTraitClass->joinData()
        );
        $this->assertEquals(
            \array_merge(
                static::JOIN_BIND_DEFAULT,
                static::JOIN_BIND
            ),
            $this->joinTraitClass->bindData()
        );
    }

    public function testInnerJoinAs()
    {
        $object = $this->joinTraitClass->innerJoinAs(
            static::JOIN_TABLE,
            static::JOIN_TABLE_ALIAS,
            static::JOIN_ON_CONDITION,
            static::JOIN_BIND
        );
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(JoinTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                static::JOIN_DEFAULT,
                [
                    \sprintf(
                        '%s %s AS %s ON (%s)',
                        JoinEnum::INNER_JOIN,
                        static::JOIN_TABLE,
                        static::JOIN_TABLE_ALIAS,
                        static::JOIN_ON_CONDITION
                    ),
                ]
            ),
            $this->joinTraitClass->joinData()
        );
        $this->assertEquals(
            \array_merge(
                static::JOIN_BIND_DEFAULT,
                static::JOIN_BIND
            ),
            $this->joinTraitClass->bindData()
        );
    }

    public function testLeftJoin()
    {
        $object = $this->joinTraitClass->leftJoin(static::JOIN_TABLE, static::JOIN_ON_CONDITION, static::JOIN_BIND);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(JoinTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                static::JOIN_DEFAULT,
                [
                    \sprintf('%s %s ON (%s)', JoinEnum::LEFT_JOIN, static::JOIN_TABLE, static::JOIN_ON_CONDITION),
                ]
            ),
            $this->joinTraitClass->joinData()
        );
        $this->assertEquals(
            \array_merge(
                static::JOIN_BIND_DEFAULT,
                static::JOIN_BIND
            ),
            $this->joinTraitClass->bindData()
        );
    }

    public function testLeftJoinAs()
    {
        $object = $this->joinTraitClass->leftJoinAs(
            static::JOIN_TABLE,
            static::JOIN_TABLE_ALIAS,
            static::JOIN_ON_CONDITION,
            static::JOIN_BIND
        );
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(JoinTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                static::JOIN_DEFAULT,
                [
                    \sprintf(
                        '%s %s AS %s ON (%s)',
                        JoinEnum::LEFT_JOIN,
                        static::JOIN_TABLE,
                        static::JOIN_TABLE_ALIAS,
                        static::JOIN_ON_CONDITION
                    ),
                ]
            ),
            $this->joinTraitClass->joinData()
        );
        $this->assertEquals(
            \array_merge(
                static::JOIN_BIND_DEFAULT,
                static::JOIN_BIND
            ),
            $this->joinTraitClass->bindData()
        );
    }

    public function testRightJoin()
    {
        $object = $this->joinTraitClass->rightJoin(static::JOIN_TABLE, static::JOIN_ON_CONDITION, static::JOIN_BIND);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(JoinTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                static::JOIN_DEFAULT,
                [
                    \sprintf('%s %s ON (%s)', JoinEnum::RIGHT_JOIN, static::JOIN_TABLE, static::JOIN_ON_CONDITION),
                ]
            ),
            $this->joinTraitClass->joinData()
        );
        $this->assertEquals(
            \array_merge(
                static::JOIN_BIND_DEFAULT,
                static::JOIN_BIND
            ),
            $this->joinTraitClass->bindData()
        );
    }

    public function testRightJoinAs()
    {
        $object = $this->joinTraitClass->rightJoinAs(
            static::JOIN_TABLE,
            static::JOIN_TABLE_ALIAS,
            static::JOIN_ON_CONDITION,
            static::JOIN_BIND
        );
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(JoinTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                static::JOIN_DEFAULT,
                [
                    \sprintf(
                        '%s %s AS %s ON (%s)',
                        JoinEnum::RIGHT_JOIN,
                        static::JOIN_TABLE,
                        static::JOIN_TABLE_ALIAS,
                        static::JOIN_ON_CONDITION
                    ),
                ]
            ),
            $this->joinTraitClass->joinData()
        );
        $this->assertEquals(
            \array_merge(
                static::JOIN_BIND_DEFAULT,
                static::JOIN_BIND
            ),
            $this->joinTraitClass->bindData()
        );
    }

    public function testCrossJoin()
    {
        $object = $this->joinTraitClass->crossJoin(static::JOIN_TABLE, static::JOIN_ON_CONDITION, static::JOIN_BIND);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(JoinTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                static::JOIN_DEFAULT,
                [
                    \sprintf('%s %s ON (%s)', JoinEnum::CROSS_JOIN, static::JOIN_TABLE, static::JOIN_ON_CONDITION),
                ]
            ),
            $this->joinTraitClass->joinData()
        );
        $this->assertEquals(
            \array_merge(
                static::JOIN_BIND_DEFAULT,
                static::JOIN_BIND
            ),
            $this->joinTraitClass->bindData()
        );
    }

    public function testCrossJoinAs()
    {
        $object = $this->joinTraitClass->crossJoinAs(
            static::JOIN_TABLE,
            static::JOIN_TABLE_ALIAS,
            static::JOIN_ON_CONDITION,
            static::JOIN_BIND
        );
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(JoinTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                static::JOIN_DEFAULT,
                [
                    \sprintf(
                        '%s %s AS %s ON (%s)',
                        JoinEnum::CROSS_JOIN,
                        static::JOIN_TABLE,
                        static::JOIN_TABLE_ALIAS,
                        static::JOIN_ON_CONDITION
                    ),
                ]
            ),
            $this->joinTraitClass->joinData()
        );
        $this->assertEquals(
            \array_merge(
                static::JOIN_BIND_DEFAULT,
                static::JOIN_BIND
            ),
            $this->joinTraitClass->bindData()
        );
    }

    public function testFullOuterJoin()
    {
        $object = $this->joinTraitClass->fullOuterJoin(
            static::JOIN_TABLE,
            static::JOIN_ON_CONDITION,
            static::JOIN_BIND
        );
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(JoinTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                static::JOIN_DEFAULT,
                [
                    \sprintf('%s %s ON (%s)', JoinEnum::FULL_OUTER_JOIN, static::JOIN_TABLE, static::JOIN_ON_CONDITION),
                ]
            ),
            $this->joinTraitClass->joinData()
        );
        $this->assertEquals(
            \array_merge(
                static::JOIN_BIND_DEFAULT,
                static::JOIN_BIND
            ),
            $this->joinTraitClass->bindData()
        );
    }

    public function testFullOuterJoinAs()
    {
        $object = $this->joinTraitClass->fullOuterJoinAs(
            static::JOIN_TABLE,
            static::JOIN_TABLE_ALIAS,
            static::JOIN_ON_CONDITION,
            static::JOIN_BIND
        );
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(JoinTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                static::JOIN_DEFAULT,
                [
                    \sprintf(
                        '%s %s AS %s ON (%s)',
                        JoinEnum::FULL_OUTER_JOIN,
                        static::JOIN_TABLE,
                        static::JOIN_TABLE_ALIAS,
                        static::JOIN_ON_CONDITION
                    ),
                ]
            ),
            $this->joinTraitClass->joinData()
        );
        $this->assertEquals(
            \array_merge(
                static::JOIN_BIND_DEFAULT,
                static::JOIN_BIND
            ),
            $this->joinTraitClass->bindData()
        );
    }
}
