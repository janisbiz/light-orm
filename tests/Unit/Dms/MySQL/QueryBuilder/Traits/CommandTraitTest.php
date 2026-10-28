<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\CommandEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\CommandTrait;
use PHPUnit\Framework\Attributes\DataProvider;

class CommandTraitTest extends AbstractTraitTestCase
{
    /**
     * @var CommandTrait
     */
    private $commandTraitClass;

    public function setUp(): void
    {
        $this->commandTraitClass = new class ()
        {
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
     * @param string $command
     * @param string $expected
     */
    #[DataProvider('setCommandData')]
    public function testSetCommand($command, $expected)
    {
        $object = $this->commandTraitClass->command($command);
        $this->assertObjectUsesTrait(CommandTrait::class, $object);
        $this->assertEquals($expected, $this->commandTraitClass->commandData());
    }

    /**
     * @param string $command
     * @param string $expected
     */
    #[DataProvider('setCommandData')]
    public function testSetCommandData($command, $expected)
    {
        $object = $this->commandTraitClass->command($command);
        $this->assertObjectUsesTrait(CommandTrait::class, $object);
        $this->assertEquals($expected, $this->commandTraitClass->commandData());
    }

    /**
     * @return array
     */
    public static function setCommandData()
    {
        return \array_map(
            fn($val) => [
                $val,
                $val
            ],
            CommandEnum::COMMANDS
        );
    }

    /**
     * @param string $command
     */
    #[DataProvider('buildCommandQueryPartData')]
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
    public static function buildCommandQueryPartData()
    {
        return \array_map(
            fn($val) => [
                $val
            ],
            CommandEnum::COMMANDS
        );
    }
}
