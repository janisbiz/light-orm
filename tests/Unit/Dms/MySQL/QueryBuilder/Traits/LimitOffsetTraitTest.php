<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\LimitOffsetTrait;

class LimitOffsetTraitTest extends AbstractTraitTestCase
{
    use LimitOffsetTrait;

    const LIMIT_EMPTY = null;
    const LIMIT_DEFAULT = 1;
    const LIMIT = 2;
    const OFFSET_EMPTY = null;
    const OFFSET = 1;

    public function testOffset()
    {
        $object = $this->limit(self::LIMIT_DEFAULT)->offset(self::OFFSET);
        $this->assertObjectUsesTrait(LimitOffsetTrait::class, $object);
        $this->assertEquals(self::OFFSET, $this->offset);
    }

    /**
     * @codeCoverageIgnore
     * @expectedException \Exception
     * @expectedExceptionMessage You must pass $offset to offset method!
     */
    public function testOffsetEmpty()
    {
        $this->offset(self::OFFSET_EMPTY);
    }

    /**
     * @codeCoverageIgnore
     * @expectedException \Exception
     * @expectedExceptionMessage You must set LIMIT before calling offset method!
     */
    public function testOffsetWithEmptyLimit()
    {
        $this->offset(self::OFFSET);
    }

    public function testLimitWithOffset()
    {
        $object = $this->limitWithOffset(self::LIMIT, self::OFFSET);
        $this->assertObjectUsesTrait(LimitOffsetTrait::class, $object);
        $this->assertEquals(self::LIMIT, $this->limit);
        $this->assertEquals(self::OFFSET, $this->offset);
    }

    /**
     * @codeCoverageIgnore
     * @expectedException \Exception
     * @expectedExceptionMessage You must pass $limit to limit method!
     */
    public function testLimitWithOffsetWithEmptyLimit()
    {
        $this->limitWithOffset(self::LIMIT_EMPTY, self::OFFSET);
    }

    /**
     * @codeCoverageIgnore
     * @expectedException \Exception
     * @expectedExceptionMessage You must pass $offset to offset method!
     */
    public function testLimitWithOffsetWithEmptyOffset()
    {
        $this->limitWithOffset(self::LIMIT, self::OFFSET_EMPTY);
    }

    public function testLimit()
    {
        $object = $this->limit(self::LIMIT);
        $this->assertObjectUsesTrait(LimitOffsetTrait::class, $object);
        $this->assertEquals(self::LIMIT, $this->limit);
    }

    /**
     * @codeCoverageIgnore
     * @expectedException \Exception
     * @expectedExceptionMessage You must pass $limit to limit method!
     */
    public function testLimitEmpty()
    {
        $this->limit(self::LIMIT_EMPTY);
    }

    public function testBuildLimitQueryPart()
    {
        $this->limit(self::LIMIT);

        $this->assertEquals(
            \sprintf('%s %d', ConditionEnum::LIMIT, $this->limit),
            $this->buildLimitQueryPart()
        );
    }

    public function testBuildLimitQueryPartWhenEmpty()
    {
        $this->assertEquals(null, $this->buildLimitQueryPart());
    }

    public function testBuildOffsetQueryPart()
    {
        $this->limitWithOffset(self::LIMIT, self::OFFSET);

        $this->assertEquals(
            \sprintf('%s %d', ConditionEnum::OFFSET, $this->offset),
            $this->buildOffsetQueryPart()
        );
    }

    public function testBuildOffsetQueryPartWhenEmpty()
    {
        $this->assertEquals(null, $this->buildOffsetQueryPart());
    }
}
