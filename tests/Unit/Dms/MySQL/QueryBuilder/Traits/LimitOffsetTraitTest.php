<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\LimitOffsetTrait;

class LimitOffsetTraitTest extends AbstractTraitTestCase
{
    use LimitOffsetTrait;

    const LIMIT_INVALID = -1;
    const LIMIT_DEFAULT = 1;
    const LIMIT = 2;
    const OFFSET_INVALID = -1;
    const OFFSET = 1;

    public function testOffset()
    {
        $object = $this->limit(static::LIMIT)->offset(static::OFFSET);
        $this->assertObjectUsesTrait(LimitOffsetTrait::class, $object);
        $this->assertEquals(static::OFFSET, $this->offset);
    }

    public function testOffsetEmpty()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('You must pass $offset to offset method!');

        $this->offset(static::OFFSET_INVALID);
    }

    public function testOffsetWithEmptyLimit()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('You must set LIMIT before calling offset method!');

        $this->offset(static::OFFSET);
    }

    public function testLimitWithOffset()
    {
        $object = $this->limitWithOffset(static::LIMIT, static::OFFSET);
        $this->assertObjectUsesTrait(LimitOffsetTrait::class, $object);
        $this->assertEquals(static::LIMIT, $this->limit);
        $this->assertEquals(static::OFFSET, $this->offset);
    }

    public function testLimitWithOffsetWithEmptyLimit()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('You must pass $limit to limit method!');

        $this->limitWithOffset(static::LIMIT_INVALID, static::OFFSET);
    }

    public function testLimitWithOffsetWithEmptyOffset()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('You must pass $offset to offset method!');

        $this->limitWithOffset(static::LIMIT, static::OFFSET_INVALID);
    }

    public function testLimit()
    {
        $object = $this->limit(static::LIMIT);
        $this->assertObjectUsesTrait(LimitOffsetTrait::class, $object);
        $this->assertEquals(static::LIMIT, $this->limit);
    }

    public function testLimitEmpty()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('You must pass $limit to limit method!');

        $this->limit(static::LIMIT_INVALID);
    }

    public function testBuildLimitQueryPart()
    {
        $this->limit(static::LIMIT);

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
        $this->limitWithOffset(static::LIMIT, static::OFFSET);

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
