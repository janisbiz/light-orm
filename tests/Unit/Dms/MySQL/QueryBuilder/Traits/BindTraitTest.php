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
        $this->bind = static::BIND_DEFAULT;
    }

    public function testBind()
    {
        $this->assertEquals(static::BIND_DEFAULT, $this->bind);

        $object = $this->bind(static::BIND);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertEquals(\array_merge(static::BIND_DEFAULT, static::BIND), $this->bind);

        $object = $this->bind(static::BIND_OVERRIDE);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertEquals(
            \array_merge(static::BIND_DEFAULT, static::BIND, static::BIND_OVERRIDE),
            $this->bind
        );
    }

    public function testBindData()
    {
        $this->assertEquals(
            static::BIND_DEFAULT,
            $this->bindData()
        );
    }
}
