<?php

namespace Janisbiz\LightOrm\Tests\Unit\Generator\Dms;

use Janisbiz\LightOrm\Generator\Dms\Column;
use Janisbiz\LightOrm\Generator\Dms\Database;
use Janisbiz\LightOrm\Generator\Dms\Table;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    const DATABASE_NAME = 'database_name_snake_case';

    /**
     * @var Table[]
     */
    private $tables = [];

    /**
     * @var Database
     */
    private $database;

    public function setUp()
    {
        $columns = [];
        for ($i = 1; $i <= 3; $i++) {
            $columns[] = new Column(
                \sprintf('%s_%d', ColumnTest::COLUMN_NAME, $i),
                ColumnTest::COLUMN_TYPE,
                ColumnTest::COLUMN_NULLABLE,
                ColumnTest::COLUMN_KEY,
                ColumnTest::COLUMN_DEFAULT,
                ColumnTest::COLUMN_EXTRA
            );
        }

        for ($i = 1; $i <=3; $i++) {
            $this->tables[] = new Table(
                \sprintf('%s_%d', TableTest::TABLE_NAME, $i),
                $columns
            );
        }

        $this->database = new Database(self::DATABASE_NAME, $this->tables);
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
        $column = new Database(
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
