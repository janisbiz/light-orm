<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

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
        $this->table = self::TABLE_DEFAULT;
    }

    public function testTable()
    {
        $this->assertEquals(self::TABLE_DEFAULT, $this->table);

        $object = $this->table(self::TABLE_ARRAY);
        $this->assertObjectUsesTrait(TableTrait::class, $object);
        $this->assertEquals(
            \array_merge(self::TABLE_DEFAULT, self::TABLE_ARRAY),
            $this->table
        );

        $object = $this->table(self::TABLE);
        $this->assertObjectUsesTrait(TableTrait::class, $object);
        $this->assertEquals(
            \array_merge(self::TABLE_DEFAULT, self::TABLE_ARRAY, [self::TABLE]),
            $this->table
        );
    }

    public function testTableClearAll()
    {
        $this->table(self::TABLE, true);
        $this->assertEquals([self::TABLE], $this->table);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage You must pass $table to table method!
     */
    public function testTableWhenEmpty()
    {
        $this->table(self::TABLE_EMPTY);
    }
}
