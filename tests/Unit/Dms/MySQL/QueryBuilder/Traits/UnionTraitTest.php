<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\UnionTrait;

class UnionTraitTest extends AbstractTraitTestCase
{
    use UnionTrait;

    public function testUnionAll()
    {
        $this->markTestIncomplete('Need to implement...');
    }

    public function testBuildUnionAllQueryPart()
    {
        $this->markTestIncomplete('Need to implement...');
    }

    public function testBuildUnionAllQueryPartWhenEmpty()
    {
        $this->unionAll = [];

        $this->assertEquals(null, $this->buildUnionAllQueryPart());
    }
}
