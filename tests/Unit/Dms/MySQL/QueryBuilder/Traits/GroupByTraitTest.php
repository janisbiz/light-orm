<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\GroupByTrait;

class GroupByTraitTest extends AbstractTraitTestCase
{
    const GROUP_BY_DEFAULT = [
        'group_by1',
        'group_by2',
    ];
    const GROUP_BY_ARRAY = [
        'group_by3',
        'group_by4',
    ];
    const GROUP_BY_EMPTY = '';
    const GROUP_BY = 'group_by5';

    /**
     * @var GroupByTrait
     */
    private $groupByTraitClass;

    public function setUp()
    {
        $this->groupByTraitClass = new class (GroupByTraitTest::GROUP_BY_DEFAULT)
        {
            use GroupByTrait;

            /**
             * @param array $groupByDefault
             */
            public function __construct(array $groupByDefault)
            {
                $this->groupBy = $groupByDefault;
            }

            /**
             * @return array
             */
            public function groupByData(): array
            {
                return $this->groupBy;
            }

            public function clearGroupByData()
            {
                $this->groupBy = [];
            }

            /**
             * @return null|string
             */
            public function buildGroupByQueryPartPublic(): ?string
            {
                return $this->buildGroupByQueryPart();
            }
        };
    }

    public function testGroupBy()
    {
        $this->assertEquals(static::GROUP_BY_DEFAULT, $this->groupByTraitClass->groupByData());

        $object = $this->groupByTraitClass->groupBy(static::GROUP_BY_ARRAY);
        $this->assertObjectUsesTrait(GroupByTrait::class, $object);
        $this->assertEquals(
            \array_merge(static::GROUP_BY_DEFAULT, static::GROUP_BY_ARRAY),
            $this->groupByTraitClass->groupByData()
        );

        $object = $this->groupByTraitClass->groupBy(static::GROUP_BY);
        $this->assertObjectUsesTrait(GroupByTrait::class, $object);
        $this->assertEquals(
            \array_merge(static::GROUP_BY_DEFAULT, static::GROUP_BY_ARRAY, [static::GROUP_BY]),
            $this->groupByTraitClass->groupByData()
        );
    }

    public function testGroupByWhenEmpty()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('You must pass $groupBy to groupBy method!');

        $this->groupByTraitClass->groupBy(static::GROUP_BY_EMPTY);
    }

    public function testBuildGroupByQueryPart()
    {
        $this
            ->groupByTraitClass
            ->groupBy(static::GROUP_BY_ARRAY)
            ->groupBy(static::GROUP_BY)
        ;

        $this->assertEquals(
            \sprintf('%s %s', ConditionEnum::GROUP_BY, \implode(', ', $this->groupByTraitClass->groupByData())),
            $this->groupByTraitClass->buildGroupByQueryPartPublic()
        );
    }

    public function testBuildGroupByQueryPartWhenEmpty()
    {
        $this->groupByTraitClass->clearGroupByData();

        $this->assertEquals(null, $this->groupByTraitClass->buildGroupByQueryPartPublic());
    }
}
