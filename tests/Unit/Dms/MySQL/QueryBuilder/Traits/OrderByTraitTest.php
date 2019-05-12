<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\Enum\KeywordEnum;
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
        $this->orderBy = self::ORDER_BY_DEFAULT;
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
                self::ORDER_BY_DEFAULT,
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

    public function orderByData()
    {
        return [
            [
                self::ORDER_BY_COLUMN,
                KeywordEnum::ASC,
            ],
            [
                self::ORDER_BY_COLUMN,
                KeywordEnum::DESC,
            ],
            [
                self::ORDER_BY_COLUMNS,
                KeywordEnum::ASC,
            ],
            [
                self::ORDER_BY_COLUMNS,
                KeywordEnum::DESC,
            ],
        ];
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage You must pass $orderBy to orderBy method!
     */
    public function testOrderByWithEmptyOrderBy()
    {
        $this->orderBy(self::ORDER_BY_EMPTY);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Invalid $keyword "INVALID" for orderBy!
     */
    public function testOrderByWithInvalidKeyword()
    {
        $this->orderBy(self::ORDER_BY_COLUMN, self::ORDER_BY_INVALID_KEYWORD);
    }
}
