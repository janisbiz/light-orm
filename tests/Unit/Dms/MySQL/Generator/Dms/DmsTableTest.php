<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\Generator\Dms;

use Janisbiz\LightOrm\Dms\MySQL\Generator\Dms\DmsColumn;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Dms\DmsTable;
use PHPUnit\Framework\TestCase;

class DmsTableTest extends TestCase
{
    const TABLE_NAME = 'table_name_snake_case';

    /**
     * @var DmsColumn[]
     */
    private $dmsColumns = [];

    /**
     * @var DmsTable
     */
    private $dmsTable;

    public function setUp()
    {
        for ($i = 1; $i <= 3; $i++) {
            $this->dmsColumns[] = new DmsColumn(
                \sprintf('%s_%d', DmsColumnTest::COLUMN_NAME, $i),
                DmsColumnTest::COLUMN_TYPE,
                DmsColumnTest::COLUMN_NULLABLE,
                DmsColumnTest::COLUMN_KEY,
                DmsColumnTest::COLUMN_DEFAULT,
                DmsColumnTest::COLUMN_EXTRA
            );
        }

        $this->dmsTable = new DmsTable(self::TABLE_NAME, $this->dmsColumns);
    }

    public function testGetName()
    {
        $this->assertEquals(self::TABLE_NAME, $this->dmsTable->getName());
    }

    /**
     * @dataProvider getPhpNameData
     *
     * @param string $name
     * @param string $phpName
     */
    public function testGetPhpName($name, $phpName)
    {
        $dmsTable = new DmsTable(
            $name,
            $this->dmsColumns
        );

        $this->assertEquals($phpName, $dmsTable->getPhpName());
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
        $this->assertEquals(\count($this->dmsColumns), \count($this->dmsTable->getDmsColumns()));
    }
}
