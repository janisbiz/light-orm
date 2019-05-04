<?php

namespace Janisbiz\LightOrm\Tests\Unit\Generator\Dms;

use Janisbiz\LightOrm\Generator\Dms\DmsColumn;
use Janisbiz\LightOrm\Generator\Dms\DmsDatabase;
use Janisbiz\LightOrm\Generator\Dms\DmsTable;
use PHPUnit\Framework\TestCase;

class DmsDatabaseTest extends TestCase
{
    const DATABASE_NAME = 'database_name_snake_case';

    /**
     * @var DmsTable[]
     */
    private $tables = [];

    /**
     * @var DmsDatabase
     */
    private $database;

    public function setUp()
    {
        $columns = [];
        for ($i = 1; $i <= 3; $i++) {
            $columns[] = new DmsColumn(
                \sprintf('%s_%d', DmsColumnTest::COLUMN_NAME, $i),
                DmsColumnTest::COLUMN_TYPE,
                DmsColumnTest::COLUMN_NULLABLE,
                DmsColumnTest::COLUMN_KEY,
                DmsColumnTest::COLUMN_DEFAULT,
                DmsColumnTest::COLUMN_EXTRA
            );
        }

        for ($i = 1; $i <=3; $i++) {
            $this->tables[] = new DmsTable(
                \sprintf('%s_%d', DmsTableTest::TABLE_NAME, $i),
                $columns
            );
        }

        $this->database = new DmsDatabase(self::DATABASE_NAME, $this->tables);
    }

    public function testGetName()
    {
        $this->assertEquals(self::DATABASE_NAME, $this->database->getName());
    }

    /**
     * @dataProvider getPhpNameData
     *
     * @param string $name
     * @param string $phpName
     */
    public function testGetPhpName($name, $phpName)
    {
        $column = new DmsDatabase(
            $name,
            $this->tables
        );

        $this->assertEquals($phpName, $column->getPhpName());
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

    public function testGetTables()
    {
        $this->assertEquals(\count($this->tables), \count($this->database->getTables()));
    }
}
