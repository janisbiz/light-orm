<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\LimitOffsetTrait;

class LimitOffsetTraitTest extends AbstractTraitTestCase
{
    const LIMIT_INVALID = -1;
    const LIMIT_DEFAULT = 1;
    const LIMIT = 2;
    const OFFSET_INVALID = -1;
    const OFFSET = 1;

    /**
     * @var LimitOffsetTrait
     */
    private $limitOffsetTraitClass;
    
    public function setUp()
    {
        $this->limitOffsetTraitClass = new class () {
            use LimitOffsetTrait;

            /**
             * @return null|int
             */
            public function limitData(): ?int
            {
                return $this->limit;
            }

            /**
             * @return null|int
             */
            public function offsetData(): ?int
            {
                return $this->offset;
            }

            /**
             * @return null|string
             */
            public function buildLimitQueryPartPublic(): ?string
            {
                return $this->buildLimitQueryPart();
            }

            /**
             * @return null|string
             */
            public function buildOffsetQueryPartPublic(): ?string
            {
                return $this->buildOffsetQueryPart();
            }
        };
    }

    public function testOffset()
    {
        $object = $this->limitOffsetTraitClass->limit(static::LIMIT)->offset(static::OFFSET);
        $this->assertObjectUsesTrait(LimitOffsetTrait::class, $object);
        $this->assertEquals(static::OFFSET, $this->limitOffsetTraitClass->offsetData());
    }

    public function testOffsetInvalid()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('You must pass $offset to offset method!');

        $this->limitOffsetTraitClass->offset(static::OFFSET_INVALID);
    }

    public function testOffsetWithEmptyLimit()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('You must set LIMIT before calling offset method!');

        $this->limitOffsetTraitClass->offset(static::OFFSET);
    }

    public function testLimitWithOffset()
    {
        $object = $this->limitOffsetTraitClass->limitWithOffset(static::LIMIT, static::OFFSET);
        $this->assertObjectUsesTrait(LimitOffsetTrait::class, $object);
        $this->assertEquals(static::LIMIT, $this->limitOffsetTraitClass->limitData());
        $this->assertEquals(static::OFFSET, $this->limitOffsetTraitClass->offsetData());
    }

    public function testLimitWithOffsetWithEmptyLimit()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('You must pass $limit to limit method!');

        $this->limitOffsetTraitClass->limitWithOffset(static::LIMIT_INVALID, static::OFFSET);
    }

    public function testLimitWithOffsetWithEmptyOffset()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('You must pass $offset to offset method!');

        $this->limitOffsetTraitClass->limitWithOffset(static::LIMIT, static::OFFSET_INVALID);
    }

    public function testLimit()
    {
        $object = $this->limitOffsetTraitClass->limit(static::LIMIT);
        $this->assertObjectUsesTrait(LimitOffsetTrait::class, $object);
        $this->assertEquals(static::LIMIT, $this->limitOffsetTraitClass->limitData());
    }

    public function testLimitEmpty()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('You must pass $limit to limit method!');

        $this->limitOffsetTraitClass->limit(static::LIMIT_INVALID);
    }

    public function testBuildLimitQueryPart()
    {
        $this->limitOffsetTraitClass->limit(static::LIMIT);

        $this->assertEquals(
            \sprintf('%s %d', ConditionEnum::LIMIT, $this->limitOffsetTraitClass->limitData()),
            $this->limitOffsetTraitClass->buildLimitQueryPartPublic()
        );
    }

    public function testBuildLimitQueryPartWhenEmpty()
    {
        $this->assertEquals(null, $this->limitOffsetTraitClass->buildLimitQueryPartPublic());
    }

    public function testBuildOffsetQueryPart()
    {
        $this->limitOffsetTraitClass->limitWithOffset(static::LIMIT, static::OFFSET);

        $this->assertEquals(
            \sprintf('%s %d', ConditionEnum::OFFSET, $this->limitOffsetTraitClass->offsetData()),
            $this->limitOffsetTraitClass->buildOffsetQueryPartPublic()
        );
    }

    public function testBuildOffsetQueryPartWhenEmpty()
    {
        $this->assertEquals(null, $this->limitOffsetTraitClass->buildOffsetQueryPartPublic());
    }
}
