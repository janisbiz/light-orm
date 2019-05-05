<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\BindTrait;

class BindTraitTest extends AbstractTraitTestCase
{
    use BindTrait;

    const BIND = [
        'key2' => 'val2',
        'key3' => 'val3',
        'key4' => 'val4',
    ];
    const BIND_DEFAULT = [
        'key1' => 'val1',
    ];
    const BIND_OVERRIDE = [
        'key1' => 'valN',
    ];

    public function setUp()
    {
        $this->bind = self::BIND_DEFAULT;
    }

    public function testBind()
    {
        $this->assertEquals(self::BIND_DEFAULT, $this->bind);

        $object = $this->bind(self::BIND);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertEquals(\array_merge(self::BIND_DEFAULT, self::BIND), $this->bind);

        $object = $this->bind(self::BIND_OVERRIDE);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertEquals(
            \array_merge(self::BIND_DEFAULT, self::BIND, self::BIND_OVERRIDE),
            $this->bind
        );
    }

    public function testBindData()
    {
        $this->assertEquals(
            self::BIND_DEFAULT,
            $this->bindData()
        );
    }
}
