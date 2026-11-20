<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

abstract class AbstractTraitTestCase extends TestCase
{
    protected array $orderBy;

    protected function assertObjectUsesTrait(string $trait, $object, string $message = '')
    {
        $traits = [];
        $this->getTraits($object, $traits);

        parent::assertThat(
            in_array($trait, $traits),
            static::isTrue(),
            mb_strlen($message)
                ? $message
                : sprintf(
                    'Failed asserting that "%s" is used in object "%s"',
                    $trait,
                    $object::class
                )
        );
    }

    private function getTraits(string|object $class, array &$traits)
    {
        $classTraits = array_keys((new ReflectionClass($class))->getTraits());

        $traits = array_merge($traits, $classTraits);

        foreach ($classTraits as $classTrait) {
            $this->getTraits($classTrait, $traits);
        }
    }
}
