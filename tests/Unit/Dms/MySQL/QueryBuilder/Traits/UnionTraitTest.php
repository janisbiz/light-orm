<?php declare(strict_types=1);

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

    /**
     * @var QueryBuilderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queryBuilder;

    /**
     * @var BindTrait|UnionTrait
     */
    private $unionTraitClass;

    public function setUp()
    {
        $this->queryBuilder = $this->createMock(QueryBuilderInterface::class);
        $this->queryBuilder->method('bindData')->willReturn(static::QUERY_BUILDER_BIND_DATA);
        $this->queryBuilder->method('buildQuery')->willReturn(static::QUERY_BUILDER_QUERY);

        $this->unionTraitClass = new class () {
            use BindTrait;
            use UnionTrait;

            /**
             * @return array
             */
            public function unionAllData(): array
            {
                return $this->unionAll;
            }

            public function clearUnionAllData()
            {
                $this->unionAll = [];
            }

            /**
             * @return null|string
             */
            public function buildUnionAllQueryPartPublic(): ?string
            {
                return $this->buildUnionAllQueryPart();
            }
        };
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
            $this->unionTraitClass->unionAll($unionAllQuery);
        }

        $this->assertCount(\count($unionAllQueries), $this->unionTraitClass->unionAllData());
        $this->assertEquals(
            \array_map(
                function (QueryBuilderInterface $unionAllQuery) {
                    return \sprintf('(%s)', $unionAllQuery->buildQuery());
                },
                $unionAllQueries
            ),
            $this->unionTraitClass->unionAllData()
        );
        $this->assertCount(\count(self::QUERY_BUILDER_BIND_DATA), $this->unionTraitClass->bindData());
        $this->assertEquals(self::QUERY_BUILDER_BIND_DATA, $this->unionTraitClass->bindData());
    }

    public function testUnionAllWhenInvalidQueryBuilderPassed()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('$queryBuilder should be with valid command! Valid command: "SELECT"');

        $this->unionTraitClass->unionAll($this->queryBuilder);
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
            $this->unionTraitClass->unionAll($unionAllQuery);
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
            $this->unionTraitClass->buildUnionAllQueryPartPublic()
        );
    }

    public function testBuildUnionAllQueryPartWhenEmpty()
    {
        $this->unionTraitClass->clearUnionAllData();

        $this->assertEquals(null, $this->unionTraitClass->buildUnionAllQueryPartPublic());
    }
}
