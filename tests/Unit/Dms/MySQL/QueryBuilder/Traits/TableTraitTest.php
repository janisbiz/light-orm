<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\TableTrait;

class TableTraitTest extends AbstractTraitTestCase
{
    const TABLE_DEFAULT = [
        'table1',
        'table2',
    ];
    const TABLE_ARRAY = [
        'table3',
        'table4',
    ];
    const TABLE_EMPTY = '';
    const TABLE = 'table5';

    /**
     * @var TableTrait
     */
    private $tableTraitClass;

    public function setUp()
    {
        $this->tableTraitClass = new class (TableTraitTest::TABLE_DEFAULT) {
            use TableTrait;

            /**
             * @param array $tableDataDefault
             */
            public function __construct(array $tableDataDefault)
            {
                $this->table = $tableDataDefault;
            }

            /**
             * @return array
             */
            public function &tableData(): array
            {
                return $this->table;
            }
            
            public function clearTableData()
            {
                $this->table = [];
            }

            /**
             * @return null|string
             */
            public function buildTableQueryPartPublic(): ?string
            {
                return $this->buildTableQueryPart();
            }

            /**
             * @return null|string
             */
            public function buildFromQueryPartPublic(): ?string
            {
                return $this->buildFromQueryPart();
            }
        };
    }

    public function testTable()
    {
        $this->assertEquals(static::TABLE_DEFAULT, $this->tableTraitClass->tableData());

        $object = $this->tableTraitClass->table(static::TABLE_ARRAY);
        $this->assertObjectUsesTrait(TableTrait::class, $object);
        $this->assertEquals(
            \array_merge(static::TABLE_DEFAULT, static::TABLE_ARRAY),
            $this->tableTraitClass->tableData()
        );

        $object = $this->tableTraitClass->table(static::TABLE);
        $this->assertObjectUsesTrait(TableTrait::class, $object);
        $this->assertEquals(
            \array_merge(static::TABLE_DEFAULT, static::TABLE_ARRAY, [static::TABLE]),
            $this->tableTraitClass->tableData()
        );
    }

    public function testTableClearAll()
    {
        $this->tableTraitClass->table(static::TABLE, true);
        $this->assertEquals([static::TABLE], $this->tableTraitClass->tableData());
    }

    public function testTableWhenEmpty()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('You must pass $table to table method!');

        $this->tableTraitClass->table(static::TABLE_EMPTY);
    }

    public function testBuildTableQueryPart()
    {
        $this->tableTraitClass->table(static::TABLE);

        $this->assertEquals(
            \reset($this->tableTraitClass->tableData()),
            $this->tableTraitClass->buildTableQueryPartPublic()
        );
    }

    public function testBuildTableQueryPartWhenEmpty()
    {
        $this->tableTraitClass->clearTableData();

        $this->assertEquals(null, $this->tableTraitClass->buildTableQueryPartPublic());
    }

    public function testBuildFromQueryPart()
    {
        $this->tableTraitClass->table(static::TABLE);

        $this->assertEquals(
            \sprintf('%s %s', ConditionEnum::FROM, \implode(', ', $this->tableTraitClass->tableData())),
            $this->tableTraitClass->buildFromQueryPartPublic()
        );
    }

    public function testBuildFromQueryPartWhenEmpty()
    {
        $this->tableTraitClass->clearTableData();

        $this->assertEquals(null, $this->tableTraitClass->buildFromQueryPartPublic());
    }
}
