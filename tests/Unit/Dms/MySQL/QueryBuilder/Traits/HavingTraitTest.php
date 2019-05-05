<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\BindTrait;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\HavingTrait;

class HavingTraitTest extends AbstractTraitTestCase
{
    use BindTrait;
    use HavingTrait;

    const HAVING_CONDITION_DEFAULT = [
        'column1 = :value1Bind',
    ];
    const HAVING_CONDITION_BIND_DEFAULT = [
        'value1Bind' => 'value1',
    ];
    const HAVING_CONDITION_EMPTY = null;
    const HAVING_CONDITION = 'column2 = :value2Bind';
    const HAVING_CONDITION_BIND = [
        'value2Bind' => 'value2',
    ];

    public function setUp()
    {
        $this->bind = self::HAVING_CONDITION_BIND_DEFAULT;
        $this->having = self::HAVING_CONDITION_DEFAULT;
    }

    public function testHaving()
    {
        $object = $this->having(self::HAVING_CONDITION, self::HAVING_CONDITION_BIND);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(HavingTrait::class, $object);
        $this->assertEquals(\array_merge(self::HAVING_CONDITION_DEFAULT, [self::HAVING_CONDITION]), $this->having);
        $this->assertEquals(
            \array_merge(self::HAVING_CONDITION_BIND_DEFAULT, self::HAVING_CONDITION_BIND),
            $this->bind
        );
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage You must pass $condition to having function!
     */
    public function testHavingWithEmptyCondition()
    {
        $this->having(self::HAVING_CONDITION_EMPTY);
    }
}
