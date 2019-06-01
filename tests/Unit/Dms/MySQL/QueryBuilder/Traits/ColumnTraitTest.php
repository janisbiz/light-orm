<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\ColumnTrait;

class ColumnTraitTest extends AbstractTraitTestCase
{
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

    /**
     * @var ColumnTrait
     */
    private $columnTraitClass;

    public function setUp()
    {
        $this->columnTraitClass = new class (ColumnTraitTest::COLUMN_DEFAULT)
        {
            use ColumnTrait;

            /**
             * @param array $columnDefault
             */
            public function __construct(array $columnDefault)
            {
                $this->column = $columnDefault;
            }

            /**
             * @return array
             */
            public function columnData(): array
            {
                return $this->column;
            }

            public function clearColumnData()
            {
                $this->column = [];
            }

            /**
             * @return string
             */
            public function buildColumnQueryPartPublic(): string
            {
                return $this->buildColumnQueryPart();
            }
        };
    }

    public function testColumn()
    {
        $this->assertEquals(static::COLUMN_DEFAULT, $this->columnTraitClass->columnData());

        $object = $this->columnTraitClass->column(static::COLUMN_ARRAY);
        $this->assertObjectUsesTrait(ColumnTrait::class, $object);
        $this->assertEquals(
            \array_merge(static::COLUMN_DEFAULT, static::COLUMN_ARRAY),
            $this->columnTraitClass->columnData()
        );

        $object = $this->columnTraitClass->column(static::COLUMN);
        $this->assertObjectUsesTrait(ColumnTrait::class, $object);
        $this->assertEquals(
            \array_merge(static::COLUMN_DEFAULT, static::COLUMN_ARRAY, [static::COLUMN]),
            $this->columnTraitClass->columnData()
        );
    }

    public function testColumnClearAll()
    {
        $this->columnTraitClass->column(static::COLUMN, true);
        $this->assertEquals([static::COLUMN], $this->columnTraitClass->columnData());
    }

    public function testColumnWhenEmpty()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('You must pass $column to column method!');

        $this->columnTraitClass->column(static::COLUMN_EMPTY);
    }

    public function testBuildColumnQueryPart()
    {
        $this->columnTraitClass->column(static::COLUMN);

        $this->assertEquals(
            \implode(', ', $this->columnTraitClass->columnData()),
            $this->columnTraitClass->buildColumnQueryPartPublic()
        );
    }

    public function testBuildColumnQueryPartWhenEmpty()
    {
        $this->columnTraitClass->clearColumnData();

        $this->assertEquals('*', $this->columnTraitClass->buildColumnQueryPartPublic());
    }
}
