<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use PHPUnit\Framework\TestCase;

abstract class AbstractTraitTest extends TestCase
{
    /**
     * @param string $trait
     * @param object $object
     * @param string $message
     */
    protected function assertObjectUsesTrait($trait, $object, $message = '')
    {
        parent::assertThat(
            \in_array(
                $trait,
                \array_keys((new \ReflectionClass($object))->getTraits())
            ),
            static::isTrue(),
            \mb_strlen($message)
                ? $message
                : \sprintf(
                    'Failed asserting that "%s" is used in object "%s"',
                    $trait,
                    \get_class($object)
                )
        );
    }
}
