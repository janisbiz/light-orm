<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\ColumnTrait;

class ColumnTraitTest extends AbstractTraitTestCase
{
    use ColumnTrait;

    const COLUMN_DEFAULT = [
        'column1',
        'column2',
    ];
    const COLUMN_ARRAY = [
        'column3',
        'column4',
    ];
    const COLUMN_EMPTY = '';
    const COLUMN = 'column5';

    public function setUp()
    {
        $this->column = self::COLUMN_DEFAULT;
    }

    public function testColumn()
    {
        $this->assertEquals(self::COLUMN_DEFAULT, $this->column);

        $object = $this->column(self::COLUMN_ARRAY);
        $this->assertObjectUsesTrait(ColumnTrait::class, $object);
        $this->assertEquals(
            \array_merge(self::COLUMN_DEFAULT, self::COLUMN_ARRAY),
            $this->column
        );

        $object = $this->column(self::COLUMN);
        $this->assertObjectUsesTrait(ColumnTrait::class, $object);
        $this->assertEquals(
            \array_merge(self::COLUMN_DEFAULT, self::COLUMN_ARRAY, [self::COLUMN]),
            $this->column
        );
    }

    public function testColumnClearAll()
    {
        $this->column(self::COLUMN, true);
        $this->assertEquals([self::COLUMN], $this->column);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage You must pass $column to column method!
     */
    public function testColumnWhenEmpty()
    {
        $this->column(self::COLUMN_EMPTY);
    }
}
