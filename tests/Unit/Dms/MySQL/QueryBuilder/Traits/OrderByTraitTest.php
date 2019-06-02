<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\Enum\KeywordEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\OrderByTrait;

class OrderByTraitTest extends AbstractTraitTestCase
{
    use OrderByTrait;

    const ORDER_BY_DEFAULT = [
        'col1 DESC',
        'col2 ASC',
    ];
    const ORDER_BY_COLUMN = 'col3';
    const ORDER_BY_COLUMNS = [
        'col4',
        'col5',
    ];
    const ORDER_BY_EMPTY = null;
    const ORDER_BY_INVALID_KEYWORD = 'INVALID';

    public function setUp()
    {
        $this->orderBy = static::ORDER_BY_DEFAULT;
    }

    /**
     * @dataProvider orderByData
     *
     * @param array|string $columns
     * @param string $keyword
     */
    public function testOrderBy($columns, $keyword)
    {
        $object = $this->orderBy($columns, $keyword);
        $this->assertObjectUsesTrait(OrderByTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                static::ORDER_BY_DEFAULT,
                \array_map(
                    function ($column) use ($keyword) {
                        return \sprintf('%s %s', $column, $keyword);
                    },
                    \is_array($columns) ? $columns : [$columns]
                )
            ),
            $this->orderBy
        );
    }

    /**
     * @dataProvider orderByData
     *
     * @param array|string $columns
     * @param string $keyword
     */
    public function testBuildOrderByQueryPart($columns, $keyword)
    {
        $this->orderBy($columns, $keyword);

        $this->assertEquals(
            \sprintf('%s %s', ConditionEnum::ORDER_BY, \implode(', ', $this->orderBy)),
            $this->buildOrderByQueryPart()
        );
    }

    public function testBuildOrderByQueryPartWhenEmpty()
    {
        $this->orderBy = [];

        $this->assertEquals(null, $this->buildOrderByQueryPart());
    }

    /**
     *
     * @return array
     */
    public function orderByData()
    {
        return [
            [
                static::ORDER_BY_COLUMN,
                KeywordEnum::ASC,
            ],
            [
                static::ORDER_BY_COLUMN,
                KeywordEnum::DESC,
            ],
            [
                static::ORDER_BY_COLUMNS,
                KeywordEnum::ASC,
            ],
            [
                static::ORDER_BY_COLUMNS,
                KeywordEnum::DESC,
            ],
        ];
    }

    public function testOrderByWithEmptyOrderBy()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('You must pass $orderBy to orderBy method!');

        $this->orderBy(static::ORDER_BY_EMPTY);
    }

    public function testOrderByWithInvalidKeyword()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('Invalid $keyword "INVALID" for orderBy!');

        $this->orderBy(static::ORDER_BY_COLUMN, static::ORDER_BY_INVALID_KEYWORD);
    }
}
