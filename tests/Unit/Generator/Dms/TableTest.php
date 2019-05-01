<?php

namespace Janisbiz\LightOrm\Tests\Unit\Generator\Dms;

use Janisbiz\LightOrm\Generator\Dms\Column;
use Janisbiz\LightOrm\Generator\Dms\Table;
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
{
    const TABLE_NAME = 'table_name_snake_case';

    /**
     * @var Column[]
     */
    private $columns = [];

    /**
     * @var Table
     */
    private $table;

    public function setUp()
    {
        for ($i = 1; $i <= 3; $i++) {
            $this->columns[] = new Column(
                \sprintf('%s_%d', ColumnTest::COLUMN_NAME, $i),
                ColumnTest::COLUMN_TYPE,
                ColumnTest::COLUMN_NULLABLE,
                ColumnTest::COLUMN_KEY,
                ColumnTest::COLUMN_DEFAULT,
                ColumnTest::COLUMN_EXTRA
            );
        }

        $this->table = new Table(self::TABLE_NAME, $this->columns);
    }

    public function testGetName()
    {
        $this->assertEquals(self::TABLE_NAME, $this->table->getName());
    }

    /**
     * @dataProvider getPhpNameData
     *
     * @param string $name
     * @param string $phpName
     */
    public function testGetPhpName($name, $phpName)
    {
        $table = new Table(
            $name,
            $this->columns
        );

        $this->assertEquals($phpName, $table->getPhpName());
    }

    /**
     * @return array
     */
    public function getPhpNameData()
    {
        return [
            [
                'name_snake_case',
                'NameSnakeCase',
            ],
            [
                'name__snake__case',
                'NameSnakeCase',
            ],
            [
                'name-snake-case',
                'NameSnakeCase',
            ],
            [
                'name1-snake2-case3',
                'Name1Snake2Case3',
            ],
            [
                '1name-2snake-3case',
                '1name2snake3case',
            ],
            [
                'name',
                'Name',
            ],
        ];
    }

    public function testGetColumns()
    {
        $this->assertEquals(\count($this->columns), \count($this->table->getColumns()));
    }
}
