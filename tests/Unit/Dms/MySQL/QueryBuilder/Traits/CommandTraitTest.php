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

            public function buildCommandQueryPartPublic(): string
            {
                return $this->buildCommandQueryPart();
            }
        };
    }

    #[DataProvider('setCommandData')]
    public function testSetCommand(string $command, mixed $expected)
    {
        $object = $this->commandTraitClass->command($command);
        $this->assertObjectUsesTrait(CommandTrait::class, $object);
        $this->assertEquals($expected, $this->commandTraitClass->commandData());
    }

    #[DataProvider('setCommandData')]
    public function testSetCommandData(string $command, mixed $expected)
    {
        $object = $this->commandTraitClass->command($command);
        $this->assertObjectUsesTrait(CommandTrait::class, $object);
        $this->assertEquals($expected, $this->commandTraitClass->commandData());
    }

    public static function setCommandData(): array
    {
        return array_map(
            fn($val) => [
                $val,
                $val
            ],
            CommandEnum::COMMANDS
        );
    }

    #[DataProvider('buildCommandQueryPartData')]
    public function testBuildCommandQueryPart(string $command)
    {
        $this->commandTraitClass->command($command);

        $this->assertEquals(
            $this->commandTraitClass->commandData(),
            $this->commandTraitClass->buildCommandQueryPartPublic()
        );
    }

    public static function buildCommandQueryPartData(): array
    {
        return array_map(
            fn($val) => [
                $val
            ],
            CommandEnum::COMMANDS
        );
    }
}
