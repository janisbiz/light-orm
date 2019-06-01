<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\BindTrait;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\WhereTrait;

class WhereTraitTest extends AbstractTraitTestCase
{
    const WHERE_CONDITION_DEFAULT = [
        'column1 = :value1Bind',
    ];
    const WHERE_CONDITION_BIND_DEFAULT = [
        'value1Bind' => 'value1',
    ];
    const WHERE_CONDITION_EMPTY = '';
    const WHERE_CONDITION = 'column2 = :value2Bind';
    const WHERE_CONDITION_BIND = [
        'value2Bind' => 'value2',
    ];
    const WHERE_IN_OR_NOT_IN_COLUMN = 'column3';
    const WHERE_IN_OR_NOT_IN_COLUMN_VALUES = [
        'value3',
        'value4',
    ];

    /**
     * @var BindTrait|WhereTrait
     */
    private $whereTraitClass;
    
    public function setUp()
    {
        $this->whereTraitClass = new class (
            WhereTraitTest::WHERE_CONDITION_BIND_DEFAULT,
            WhereTraitTest::WHERE_CONDITION_DEFAULT
        ) {
            use BindTrait;
            use WhereTrait;

            /**
             * @param array $bindDataDefault
             * @param array $whereDataDefault
             */
            public function __construct(array $bindDataDefault, array $whereDataDefault)
            {
                $this->bind = $bindDataDefault;
                $this->where = $whereDataDefault;
            }

            /**
             * @return array
             */
            public function whereData(): array
            {
                return $this->where;
            }
            
            public function clearWhereData()
            {
                $this->where = [];
            }

            /**
             * @return null|string
             */
            public function buildWhereQueryPartPublic(): ?string
            {
                return $this->buildWhereQueryPart();
            }
        };
    }
    
    public function testWhere()
    {
        $object = $this->whereTraitClass->where(static::WHERE_CONDITION, static::WHERE_CONDITION_BIND);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(WhereTrait::class, $object);
        $this->assertEquals(
            \array_merge(static::WHERE_CONDITION_DEFAULT, [static::WHERE_CONDITION]),
            $this->whereTraitClass->whereData()
        );
        $this->assertEquals(
            \array_merge(static::WHERE_CONDITION_BIND_DEFAULT, static::WHERE_CONDITION_BIND),
            $this->whereTraitClass->bindData()
        );
    }

    public function testWhereWithEmptyCondition()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('You must pass $condition name to where method!');

        $this->whereTraitClass->where(static::WHERE_CONDITION_EMPTY);
    }

    public function testWhereIn()
    {
        $object = $this
            ->whereTraitClass
            ->whereIn(
                static::WHERE_IN_OR_NOT_IN_COLUMN,
                static::WHERE_IN_OR_NOT_IN_COLUMN_VALUES
            )
        ;
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
            $this->whereTraitClass->whereData()
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
            $this->whereTraitClass->bindData()
        );
    }

    public function testWhereNotIn()
    {
        $object = $this
            ->whereTraitClass
            ->whereNotIn(
                static::WHERE_IN_OR_NOT_IN_COLUMN,
                static::WHERE_IN_OR_NOT_IN_COLUMN_VALUES
            )
        ;
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
            $this->whereTraitClass->whereData()
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
            $this->whereTraitClass->bindData()
        );
    }

    public function testBuildWhereQueryPart()
    {
        $this
            ->whereTraitClass
            ->where(static::WHERE_CONDITION, static::WHERE_CONDITION_BIND)
            ->whereIn(static::WHERE_IN_OR_NOT_IN_COLUMN, static::WHERE_IN_OR_NOT_IN_COLUMN_VALUES)
            ->whereNotIn(static::WHERE_IN_OR_NOT_IN_COLUMN, static::WHERE_IN_OR_NOT_IN_COLUMN_VALUES)
        ;

        $this->assertEquals(
            \sprintf(
                '%s %s',
                ConditionEnum::WHERE,
                \implode(' AND ', \array_unique($this->whereTraitClass->whereData()))
            ),
            $this->whereTraitClass->buildWhereQueryPartPublic()
        );
    }

    public function testBuildWhereQueryPartWhenEmpty()
    {
        $this->whereTraitClass->clearWhereData();

        $this->assertEquals(null, $this->whereTraitClass->buildWhereQueryPartPublic());
    }
}
