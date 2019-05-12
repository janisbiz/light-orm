<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\GroupByTrait;

class GroupByTraitTest extends AbstractTraitTestCase
{
    use GroupByTrait;
    
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

    public function setUp()
    {
        $this->groupBy = self::GROUP_BY_DEFAULT;
    }

    public function testGroupBy()
    {
        $this->assertEquals(self::GROUP_BY_DEFAULT, $this->groupBy);

        $object = $this->groupBy(self::GROUP_BY_ARRAY);
        $this->assertObjectUsesTrait(GroupByTrait::class, $object);
        $this->assertEquals(
            \array_merge(self::GROUP_BY_DEFAULT, self::GROUP_BY_ARRAY),
            $this->groupBy
        );

        $object = $this->groupBy(self::GROUP_BY);
        $this->assertObjectUsesTrait(GroupByTrait::class, $object);
        $this->assertEquals(
            \array_merge(self::GROUP_BY_DEFAULT, self::GROUP_BY_ARRAY, [self::GROUP_BY]),
            $this->groupBy
        );
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage You must pass $groupBy to groupBy method!
     */
    public function testGroupByWhenEmpty()
    {
        $this->groupBy(self::GROUP_BY_EMPTY);
    }

    public function testBuildGroupByQueryPart()
    {
        $this
            ->groupBy(self::GROUP_BY_ARRAY)
            ->groupBy(self::GROUP_BY)
        ;

        $this->assertEquals(
            \sprintf('%s %s', ConditionEnum::GROUP_BY, \implode(', ', $this->groupBy)),
            $this->buildGroupByQueryPart()
        );
    }

    public function testBuildGroupByQueryPartWhenEmpty()
    {
        $this->groupBy = [];

        $this->assertEquals(null, $this->buildGroupByQueryPart());
    }
}
