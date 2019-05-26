<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\TableTrait;

class TableTraitTest extends AbstractTraitTestCase
{
    use TableTrait;

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

    public function setUp()
    {
        $this->table = static::TABLE_DEFAULT;
    }

    public function testTable()
    {
        $this->assertEquals(static::TABLE_DEFAULT, $this->table);

        $object = $this->table(static::TABLE_ARRAY);
        $this->assertObjectUsesTrait(TableTrait::class, $object);
        $this->assertEquals(
            \array_merge(static::TABLE_DEFAULT, static::TABLE_ARRAY),
            $this->table
        );

        $object = $this->table(static::TABLE);
        $this->assertObjectUsesTrait(TableTrait::class, $object);
        $this->assertEquals(
            \array_merge(static::TABLE_DEFAULT, static::TABLE_ARRAY, [static::TABLE]),
            $this->table
        );
    }

    public function testTableClearAll()
    {
        $this->table(static::TABLE, true);
        $this->assertEquals([static::TABLE], $this->table);
    }

    public function testTableWhenEmpty()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('You must pass $table to table method!');

        $this->table(static::TABLE_EMPTY);
    }

    public function testBuildTableQueryPart()
    {
        $this->table(static::TABLE);

        $this->assertEquals(\reset($this->table), $this->buildTableQueryPart());
    }

    public function testBuildTableQueryPartWhenEmpty()
    {
        $this->table = [];

        $this->assertEquals(null, $this->buildTableQueryPart());
    }

    public function testBuildFromQueryPart()
    {
        $this->table(static::TABLE);

        $this->assertEquals(
            \sprintf('%s %s', ConditionEnum::FROM, \implode(', ', $this->table)),
            $this->buildFromQueryPart()
        );
    }

    public function testBuildFromQueryPartWhenEmpty()
    {
        $this->table = [];

        $this->assertEquals(null, $this->buildFromQueryPart());
    }
}
