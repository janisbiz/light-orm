<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\CommandEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\CommandTrait;

class CommandTraitTest extends AbstractTraitTest
{
    use CommandTrait;

    /**
     * @dataProvider setCommandData
     *
     * @param string $command
     * @param string $expected
     */
    public function testSetCommand($command, $expected)
    {
        $object = $this->setCommand($command);
        $this->assertObjectUsesTrait(CommandTrait::class, $object);
        $this->assertEquals($expected, $this->command);
    }

    /**
     * @return array
     */
    public function setCommandData()
    {

        return \array_map(
            function ($val) {
                return [
                    $val,
                    $val
                ];
            },
            CommandEnum::COMMANDS
        );
    }
}
