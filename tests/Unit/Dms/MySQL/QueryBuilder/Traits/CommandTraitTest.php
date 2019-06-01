<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\CommandEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\CommandTrait;

class CommandTraitTest extends AbstractTraitTestCase
{
    /**
     * @var CommandTrait
     */
    private $commandTraitClass;

    public function setUp()
    {
        $this->commandTraitClass = new class () {
            use CommandTrait;

            /**
             * @return string
             */
            public function buildCommandQueryPartPublic(): string
            {
                return $this->buildCommandQueryPart();
            }
        };
    }

    /**
     * @dataProvider setCommandData
     *
     * @param string $command
     * @param string $expected
     */
    public function testSetCommand($command, $expected)
    {
        $object = $this->commandTraitClass->command($command);
        $this->assertObjectUsesTrait(CommandTrait::class, $object);
        $this->assertEquals($expected, $this->commandTraitClass->commandData());
    }

    /**
     * @dataProvider setCommandData
     *
     * @param string $command
     * @param string $expected
     */
    public function testSetCommandData($command, $expected)
    {
        $object = $this->commandTraitClass->command($command);
        $this->assertObjectUsesTrait(CommandTrait::class, $object);
        $this->assertEquals($expected, $this->commandTraitClass->commandData());
    }

    /**
     *
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

    /**
     * @dataProvider buildCommandQueryPartData
     *
     * @param string $command
     */
    public function testBuildCommandQueryPart($command)
    {
        $this->commandTraitClass->command($command);

        $this->assertEquals(
            $this->commandTraitClass->commandData(),
            $this->commandTraitClass->buildCommandQueryPartPublic()
        );
    }

    /**
     *
     * @return array
     */
    public function buildCommandQueryPartData()
    {
        return \array_map(
            function ($val) {
                return [
                    $val
                ];
            },
            CommandEnum::COMMANDS
        );
    }
}
