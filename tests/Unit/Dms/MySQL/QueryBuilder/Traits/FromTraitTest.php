<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\FromTrait;

class FromTraitTest extends AbstractTraitTest
{
    use FromTrait;

    const FROM_DEFAULT = [
        'from1',
        'from2',
    ];
    const FROM_ARRAY = [
        'from3',
        'from4',
    ];
    const FROM = 'from5';

    public function setUp()
    {
        $this->from = self::FROM_DEFAULT;
    }

    public function testFrom()
    {
        $this->assertEquals(self::FROM_DEFAULT, $this->from);

        $object = $this->from(self::FROM_ARRAY);
        $this->assertObjectUsesTrait(FromTrait::class, $object);
        $this->assertEquals(
            \array_merge(self::FROM_DEFAULT, self::FROM_ARRAY),
            $this->from
        );

        $object = $this->from(self::FROM);
        $this->assertObjectUsesTrait(FromTrait::class, $object);
        $this->assertEquals(
            \array_merge(self::FROM_DEFAULT, self::FROM_ARRAY, [self::FROM]),
            $this->from
        );
    }

    public function testFromClearAll()
    {
        $this->from(self::FROM, true);
        $this->assertEquals([self::FROM], $this->from);
    }
}
