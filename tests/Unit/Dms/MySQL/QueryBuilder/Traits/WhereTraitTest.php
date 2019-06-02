<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\BindTrait;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\WhereTrait;

class WhereTraitTest extends AbstractTraitTestCase
{
    use BindTrait;
    use WhereTrait;
    
    const WHERE_CONDITION_DEFAULT = [
        'column1 = :value1Bind',
    ];
    const WHERE_CONDITION_BIND_DEFAULT = [
        'value1Bind' => 'value1',
    ];
    const WHERE_CONDITION_EMPTY = null;
    const WHERE_CONDITION = 'column2 = :value2Bind';
    const WHERE_CONDITION_BIND = [
        'value2Bind' => 'value2',
    ];
    const WHERE_IN_OR_NOT_IN_COLUMN = 'column3';
    const WHERE_IN_OR_NOT_IN_COLUMN_VALUES = [
        'value3',
        'value4',
    ];

    public function setUp()
    {
        $this->bind = static::WHERE_CONDITION_BIND_DEFAULT;
        $this->where = static::WHERE_CONDITION_DEFAULT;
    }
    
    public function testWhere()
    {
        $object = $this->where(static::WHERE_CONDITION, static::WHERE_CONDITION_BIND);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(WhereTrait::class, $object);
        $this->assertEquals(\array_merge(static::WHERE_CONDITION_DEFAULT, [static::WHERE_CONDITION]), $this->where);
        $this->assertEquals(
            \array_merge(static::WHERE_CONDITION_BIND_DEFAULT, static::WHERE_CONDITION_BIND),
            $this->bind
        );
    }

    public function testWhereWithEmptyCondition()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('You must pass $condition name to where method!');

        $this->where(static::WHERE_CONDITION_EMPTY);
    }

    public function testWhereIn()
    {
        $object = $this->whereIn(static::WHERE_IN_OR_NOT_IN_COLUMN, static::WHERE_IN_OR_NOT_IN_COLUMN_VALUES);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(WhereTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                static::WHERE_CONDITION_DEFAULT,
                [
                    \sprintf(
                        '%s IN (%s)',
                        static::WHERE_IN_OR_NOT_IN_COLUMN,
                        \implode(', ', \array_map(
                            function ($i) {
                                return \sprintf(':%d_%s_In', $i, static::WHERE_IN_OR_NOT_IN_COLUMN);
                            },
                            \array_keys(static::WHERE_IN_OR_NOT_IN_COLUMN_VALUES)
                        ))
                    ),
                ]
            ),
            $this->where
        );
        $this->assertEquals(
            \array_merge(
                static::WHERE_CONDITION_BIND_DEFAULT,
                \array_reduce(
                    \array_map(
                        function ($value, $i) {
                            return [
                                \sprintf('%d_%s_In', $i, static::WHERE_IN_OR_NOT_IN_COLUMN) => $value,
                            ];
                        },
                        static::WHERE_IN_OR_NOT_IN_COLUMN_VALUES,
                        \array_keys(static::WHERE_IN_OR_NOT_IN_COLUMN_VALUES)
                    ),
                    'array_merge',
                    []
                )
            ),
            $this->bind
        );
    }

    public function testWhereNotIn()
    {
        $object = $this->whereNotIn(static::WHERE_IN_OR_NOT_IN_COLUMN, static::WHERE_IN_OR_NOT_IN_COLUMN_VALUES);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(WhereTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                static::WHERE_CONDITION_DEFAULT,
                [
                    \sprintf(
                        '%s NOT IN (%s)',
                        static::WHERE_IN_OR_NOT_IN_COLUMN,
                        \implode(', ', \array_map(
                            function ($i) {
                                return \sprintf(':%d_%s_NotIn', $i, static::WHERE_IN_OR_NOT_IN_COLUMN);
                            },
                            \array_keys(static::WHERE_IN_OR_NOT_IN_COLUMN_VALUES)
                        ))
                    ),
                ]
            ),
            $this->where
        );
        $this->assertEquals(
            \array_merge(
                static::WHERE_CONDITION_BIND_DEFAULT,
                \array_reduce(
                    \array_map(
                        function ($value, $i) {
                            return [
                                \sprintf('%d_%s_NotIn', $i, static::WHERE_IN_OR_NOT_IN_COLUMN) => $value,
                            ];
                        },
                        static::WHERE_IN_OR_NOT_IN_COLUMN_VALUES,
                        \array_keys(static::WHERE_IN_OR_NOT_IN_COLUMN_VALUES)
                    ),
                    'array_merge',
                    []
                )
            ),
            $this->bind
        );
    }

    public function testBuildWhereQueryPart()
    {
        $this
            ->where(static::WHERE_CONDITION, static::WHERE_CONDITION_BIND)
            ->whereIn(static::WHERE_IN_OR_NOT_IN_COLUMN, static::WHERE_IN_OR_NOT_IN_COLUMN_VALUES)
            ->whereNotIn(static::WHERE_IN_OR_NOT_IN_COLUMN, static::WHERE_IN_OR_NOT_IN_COLUMN_VALUES)
        ;

        $this->assertEquals(
            \sprintf('%s %s', ConditionEnum::WHERE, \implode(' AND ', \array_unique($this->where))),
            $this->buildWhereQueryPart()
        );
    }

    public function testBuildWhereQueryPartWhenEmpty()
    {
        $this->where = [];

        $this->assertEquals(null, $this->buildWhereQueryPart());
    }
}
