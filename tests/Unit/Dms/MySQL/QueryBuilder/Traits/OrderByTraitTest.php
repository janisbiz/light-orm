<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\Enum\KeywordEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\OrderByTrait;
use PHPUnit\Framework\Attributes\DataProvider;

class OrderByTraitTest extends AbstractTraitTestCase
{
    const array ORDER_BY_DEFAULT = [
        'col1 DESC',
        'col2 ASC',
    ];
    const string ORDER_BY_COLUMN = 'col3';
    const array ORDER_BY_COLUMNS = [
        'col4',
        'col5',
    ];
    const ?string ORDER_BY_EMPTY = null;
    const string ORDER_BY_INVALID_KEYWORD = 'INVALID';

    /**
     * @var OrderByTrait
     */
    private $orderByTraitClass;

    public function setUp(): void
    {
        $this->orderBy = static::ORDER_BY_DEFAULT;
        $this->orderByTraitClass = new class (OrderByTraitTest::ORDER_BY_DEFAULT)
        {
            use OrderByTrait;

            public function __construct(array $orderByDataDefault)
            {
                $this->orderBy = $orderByDataDefault;
            }

            public function orderByData(): array
            {
                return $this->orderBy;
            }

            public function clearOrderByData()
            {
                $this->orderBy = [];
            }

            public function buildOrderByQueryPartPublic(): ?string
            {
                return $this->buildOrderByQueryPart();
            }
        };
    }

    #[DataProvider('orderByData')]
    public function testOrderBy(array|string $columns, string $keyword)
    {
        $object = $this->orderByTraitClass->orderBy($columns, $keyword);
        $this->assertObjectUsesTrait(OrderByTrait::class, $object);
        $this->assertEquals(
            array_merge(
                static::ORDER_BY_DEFAULT,
                array_map(
                    fn($column) => sprintf('%s %s', $column, $keyword),
                    is_array($columns) ? $columns : [$columns]
                )
            ),
            $this->orderByTraitClass->orderByData()
        );
    }

    #[DataProvider('orderByData')]
    public function testBuildOrderByQueryPart(array|string $columns, string $keyword)
    {
        $this->orderByTraitClass->orderBy($columns, $keyword);

        $this->assertEquals(
            sprintf('%s %s', ConditionEnum::ORDER_BY, implode(', ', $this->orderByTraitClass->orderByData())),
            $this->orderByTraitClass->buildOrderByQueryPartPublic()
        );
    }

    public function testBuildOrderByQueryPartWhenEmpty()
    {
        $this->orderByTraitClass->clearOrderByData();

        $this->assertEquals(null, $this->orderByTraitClass->buildOrderByQueryPartPublic());
    }

    public static function orderByData(): array
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

        $this->orderByTraitClass->orderBy(static::ORDER_BY_EMPTY);
    }

    public function testOrderByWithInvalidKeyword()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('Invalid $keyword "INVALID" for orderBy!');

        $this->orderByTraitClass->orderBy(static::ORDER_BY_COLUMN, static::ORDER_BY_INVALID_KEYWORD);
    }
}
