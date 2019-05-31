<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\BindTrait;

class BindTraitTest extends AbstractTraitTestCase
{
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

    /**
     * @var BindTrait
     */
    private $bindTraitClass;

    public function setUp()
    {
        $this->bindTraitClass = new class (BindTraitTest::BIND_DEFAULT) {
            use BindTrait;

            /**
             * @param array $bindDefault
             */
            public function __construct(array $bindDefault)
            {
                $this->bind = $bindDefault;
            }
        };
    }

    public function testBind()
    {
        $this->assertEquals(static::BIND_DEFAULT, $this->bindTraitClass->bindData());

        $object = $this->bindTraitClass->bind(static::BIND);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertEquals(\array_merge(static::BIND_DEFAULT, static::BIND), $this->bindTraitClass->bindData());

        $object = $this->bindTraitClass->bind(static::BIND_OVERRIDE);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertEquals(
            \array_merge(static::BIND_DEFAULT, static::BIND, static::BIND_OVERRIDE),
            $this->bindTraitClass->bindData()
        );
    }

    public function testBindData()
    {
        $this->assertEquals(
            static::BIND_DEFAULT,
            $this->bindTraitClass->bindData()
        );
    }
}
