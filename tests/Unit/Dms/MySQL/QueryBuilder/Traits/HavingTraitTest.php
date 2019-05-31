<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\BindTrait;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\HavingTrait;

class HavingTraitTest extends AbstractTraitTestCase
{
    const HAVING_CONDITION_DEFAULT = [
        'column1 = :value1Bind',
    ];
    const HAVING_CONDITION_BIND_DEFAULT = [
        'value1Bind' => 'value1',
    ];
    const HAVING_CONDITION_EMPTY = '';
    const HAVING_CONDITION = 'column2 = :value2Bind';
    const HAVING_CONDITION_BIND = [
        'value2Bind' => 'value2',
    ];

    /**
     * @var HavingTrait|BindTrait
     */
    private $havingTraitClass;

    public function setUp()
    {
        $this->havingTraitClass = new class (
            HavingTraitTest::HAVING_CONDITION_BIND_DEFAULT,
            HavingTraitTest::HAVING_CONDITION_DEFAULT
        ) {
            use BindTrait;
            use HavingTrait;

            /**
             * @param array $bindDefaultData
             * @param array $havingDefaultData
             */
            public function __construct(array $bindDefaultData, array $havingDefaultData)
            {
                $this->bind = $bindDefaultData;
                $this->having = $havingDefaultData;
            }

            /**
             * @return array
             */
            public function havingData(): array
            {
                return $this->having;
            }

            public function clearHavingData()
            {
                $this->having = [];
            }

            /**
             * @return null|string
             */
            public function buildHavingQueryPartPublic(): ?string
            {
                return $this->buildHavingQueryPart();
            }
        };
    }

    public function testHaving()
    {
        $object = $this->havingTraitClass->having(static::HAVING_CONDITION, static::HAVING_CONDITION_BIND);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(HavingTrait::class, $object);
        $this->assertEquals(
            \array_merge(static::HAVING_CONDITION_DEFAULT, [static::HAVING_CONDITION]),
            $this->havingTraitClass->havingData()
        );
        $this->assertEquals(
            \array_merge(static::HAVING_CONDITION_BIND_DEFAULT, static::HAVING_CONDITION_BIND),
            $this->havingTraitClass->bindData()
        );
    }

    public function testHavingWithEmptyCondition()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('You must pass $condition to having function!');

        $this->havingTraitClass->having(static::HAVING_CONDITION_EMPTY);
    }

    public function testBuildHavingQueryPart()
    {
        $this->havingTraitClass->having(static::HAVING_CONDITION, static::HAVING_CONDITION_BIND);

        $this->assertEquals(
            \sprintf(
                '%s %s',
                ConditionEnum::HAVING,
                \implode(' AND ', \array_unique($this->havingTraitClass->havingData()))
            ),
            $this->havingTraitClass->buildHavingQueryPartPublic()
        );
    }

    public function testBuildHavingQueryPartWhenEmpty()
    {
        $this->havingTraitClass->clearHavingData();

        $this->assertEquals(null, $this->havingTraitClass->buildHavingQueryPartPublic());
    }
}
