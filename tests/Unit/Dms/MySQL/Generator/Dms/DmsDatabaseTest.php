<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\Generator\Dms;

use Janisbiz\LightOrm\Dms\MySQL\Generator\Dms\DmsColumn;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Dms\DmsDatabase;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Dms\DmsTable;
use PHPUnit\Framework\TestCase;

class DmsDatabaseTest extends TestCase
{
    const DATABASE_NAME = 'database_name_snake_case';

    /**
     * @var DmsTable[]
     */
    private $dmsTables = [];

    /**
     * @var DmsDatabase
     */
    private $dmsDatabase;

    public function setUp()
    {
        $dmsColumns = [];
        for ($i = 1; $i <= 3; $i++) {
            $dmsColumns[] = new DmsColumn(
                \sprintf('%s_%d', DmsColumnTest::COLUMN_NAME, $i),
                DmsColumnTest::COLUMN_TYPE,
                DmsColumnTest::COLUMN_NULLABLE,
                DmsColumnTest::COLUMN_KEY,
                DmsColumnTest::COLUMN_DEFAULT,
                DmsColumnTest::COLUMN_EXTRA
            );
        }

        for ($i = 1; $i <= 3; $i++) {
            $this->dmsTables[] = new DmsTable(
                \sprintf('%s_%d', DmsTableTest::TABLE_NAME, $i),
                $dmsColumns
            );
        }

        $this->dmsDatabase = new DmsDatabase(static::DATABASE_NAME, $this->dmsTables);
    }

    public function testGetName()
    {
        $this->assertEquals(static::DATABASE_NAME, $this->dmsDatabase->getName());
    }

    /**
     * @dataProvider getPhpNameData
     *
     * @param string $name
     * @param string $phpName
     */
    public function testGetPhpName(string $name, string $phpName)
    {
        $dmsDatabase = new DmsDatabase(
            $name,
            $this->dmsTables
        );

        $this->assertEquals($phpName, $dmsDatabase->getPhpName());
    }

    /**
     *
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
        $this->assertEquals(\count($this->dmsTables), \count($this->dmsDatabase->getDmsTables()));
    }
}
