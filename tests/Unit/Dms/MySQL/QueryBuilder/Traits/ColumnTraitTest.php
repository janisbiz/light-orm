<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;
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
        $this->column = static::COLUMN_DEFAULT;
    }

    public function testColumn()
    {
        $this->assertEquals(static::COLUMN_DEFAULT, $this->column);

        $object = $this->column(static::COLUMN_ARRAY);
        $this->assertObjectUsesTrait(ColumnTrait::class, $object);
        $this->assertEquals(
            \array_merge(static::COLUMN_DEFAULT, static::COLUMN_ARRAY),
            $this->column
        );

        $object = $this->column(static::COLUMN);
        $this->assertObjectUsesTrait(ColumnTrait::class, $object);
        $this->assertEquals(
            \array_merge(static::COLUMN_DEFAULT, static::COLUMN_ARRAY, [static::COLUMN]),
            $this->column
        );
    }

    public function testColumnClearAll()
    {
        $this->column(static::COLUMN, true);
        $this->assertEquals([static::COLUMN], $this->column);
    }

    public function testColumnWhenEmpty()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('You must pass $column to column method!');

        $this->column(static::COLUMN_EMPTY);
    }

    public function testBuildColumnQueryPart()
    {
        $this->column(static::COLUMN);

        $this->assertEquals(\implode(', ', $this->column), $this->buildColumnQueryPart());
    }

    public function testBuildColumnQueryPartWhenEmpty()
    {
        $this->column = null;

        $this->assertEquals('*', $this->buildColumnQueryPart());
    }
}
