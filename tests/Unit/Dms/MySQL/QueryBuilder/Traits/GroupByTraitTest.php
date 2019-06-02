<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;
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
        $this->groupBy = static::GROUP_BY_DEFAULT;
    }

    public function testGroupBy()
    {
        $this->assertEquals(static::GROUP_BY_DEFAULT, $this->groupBy);

        $object = $this->groupBy(static::GROUP_BY_ARRAY);
        $this->assertObjectUsesTrait(GroupByTrait::class, $object);
        $this->assertEquals(
            \array_merge(static::GROUP_BY_DEFAULT, static::GROUP_BY_ARRAY),
            $this->groupBy
        );

        $object = $this->groupBy(static::GROUP_BY);
        $this->assertObjectUsesTrait(GroupByTrait::class, $object);
        $this->assertEquals(
            \array_merge(static::GROUP_BY_DEFAULT, static::GROUP_BY_ARRAY, [static::GROUP_BY]),
            $this->groupBy
        );
    }

    public function testGroupByWhenEmpty()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('You must pass $groupBy to groupBy method!');

        $this->groupBy(static::GROUP_BY_EMPTY);
    }

    public function testBuildGroupByQueryPart()
    {
        $this
            ->groupBy(static::GROUP_BY_ARRAY)
            ->groupBy(static::GROUP_BY)
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
