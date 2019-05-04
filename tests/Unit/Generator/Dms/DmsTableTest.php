<?php

namespace Janisbiz\LightOrm\Tests\Unit\Generator\Dms;

use Janisbiz\LightOrm\Generator\Dms\DmsColumn;
use Janisbiz\LightOrm\Generator\Dms\DmsTable;
use PHPUnit\Framework\TestCase;

class DmsTableTest extends TestCase
{
    const TABLE_NAME = 'table_name_snake_case';

    /**
     * @var DmsColumn[]
     */
    private $columns = [];

    /**
     * @var DmsTable
     */
    private $table;

    public function setUp()
    {
        for ($i = 1; $i <= 3; $i++) {
            $this->columns[] = new DmsColumn(
                \sprintf('%s_%d', DmsColumnTest::COLUMN_NAME, $i),
                DmsColumnTest::COLUMN_TYPE,
                DmsColumnTest::COLUMN_NULLABLE,
                DmsColumnTest::COLUMN_KEY,
                DmsColumnTest::COLUMN_DEFAULT,
                DmsColumnTest::COLUMN_EXTRA
            );
        }

        $this->table = new DmsTable(self::TABLE_NAME, $this->columns);
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
        $table = new DmsTable(
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
