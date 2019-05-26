<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\CommandEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderInterface;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\BindTrait;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\UnionTrait;

class UnionTraitTest extends AbstractTraitTestCase
{
    const COMMAND_INVALID = 'INVALID';

    const QUERY_BUILDER_COMMAND = CommandEnum::SELECT;
    const QUERY_BUILDER_QUERY = <<<MySQL
SELECT col1, col2, col3 FROM table1 WHERE table1.col1 = :col1 AND table1.col2 IS NOT NULL
MySQL;
    const QUERY_BUILDER_BIND_DATA = [
        'col1' => 'val1',
    ];

    use UnionTrait;
    use BindTrait;

    /**
     * @var QueryBuilderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queryBuilder;

    public function setUp()
    {
        $this->queryBuilder = $this->createMock(QueryBuilderInterface::class);
        $this->queryBuilder->method('bindData')->willReturn(static::QUERY_BUILDER_BIND_DATA);
        $this->queryBuilder->method('buildQuery')->willReturn(static::QUERY_BUILDER_QUERY);
    }

    public function testUnionAll()
    {
        $this->queryBuilder->method('commandData')->willReturn(static::QUERY_BUILDER_COMMAND);

        $unionAllQueries = [
            $this->queryBuilder,
            $this->queryBuilder,
            $this->queryBuilder,
        ];

        foreach ($unionAllQueries as $unionAllQuery) {
            $this->unionAll($unionAllQuery);
        }

        $this->assertCount(\count($unionAllQueries), $this->unionAll);
        $this->assertEquals(
            \array_map(
                function (QueryBuilderInterface $unionAllQuery) {
                    return \sprintf('(%s)', $unionAllQuery->buildQuery());
                },
                $unionAllQueries
            ),
            $this->unionAll
        );
        $this->assertCount(\count(self::QUERY_BUILDER_BIND_DATA), $this->bindData());
        $this->assertEquals(self::QUERY_BUILDER_BIND_DATA, $this->bindData());
    }

    public function testUnionAllWhenInvalidQueryBuilderPassed()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('$queryBuilder should be with valid command! Valid command: "SELECT"');

        $this->unionAll($this->queryBuilder);
    }

    public function testBuildUnionAllQueryPart()
    {
        $this->queryBuilder->method('commandData')->willReturn(static::QUERY_BUILDER_COMMAND);

        $unionAllQueries = [
            $this->queryBuilder,
            $this->queryBuilder,
            $this->queryBuilder,
        ];

        foreach ($unionAllQueries as $unionAllQuery) {
            $this->unionAll($unionAllQuery);
        }

        $this->assertEquals(
            \implode(
                ' UNION ALL ',
                \array_map(
                    function (QueryBuilderInterface $unionAllQuery) {
                        return \sprintf('(%s)', $unionAllQuery->buildQuery());
                    },
                    $unionAllQueries
                )
            ),
            $this->buildUnionAllQueryPart()
        );
    }

    public function testBuildUnionAllQueryPartWhenEmpty()
    {
        $this->unionAll = [];

        $this->assertEquals(null, $this->buildUnionAllQueryPart());
    }
}
