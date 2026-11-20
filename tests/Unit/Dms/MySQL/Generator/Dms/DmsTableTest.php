<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\Generator\Dms;

use Janisbiz\LightOrm\Dms\MySQL\Generator\Dms\DmsColumn;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Dms\DmsTable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DmsTableTest extends TestCase
{
    const string TABLE_NAME = 'table_name_snake_case';

    /**
     * @var DmsColumn[]
     */
    private array $dmsColumns = [];

    private DmsTable $dmsTable;

    public function setUp(): void
    {
        for ($i = 1; $i <= 3; $i++) {
            $this->dmsColumns[] = new DmsColumn(
                sprintf('%s_%d', DmsColumnTest::COLUMN_NAME, $i),
                DmsColumnTest::COLUMN_TYPE,
                DmsColumnTest::COLUMN_NULLABLE,
                DmsColumnTest::COLUMN_KEY,
                DmsColumnTest::COLUMN_DEFAULT,
                DmsColumnTest::COLUMN_EXTRA
            );
        }

        $this->dmsTable = new DmsTable(static::TABLE_NAME, $this->dmsColumns);
    }

    public function testGetName()
    {
        $this->assertEquals(static::TABLE_NAME, $this->dmsTable->getName());
    }

    #[DataProvider('getPhpNameData')]
    public function testGetPhpName(string $name, string $phpName)
    {
        $dmsTable = new DmsTable(
            $name,
            $this->dmsColumns
        );

        $this->assertEquals($phpName, $dmsTable->getPhpName());
    }

    public static function getPhpNameData(): array
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
        $this->assertEquals(count($this->dmsColumns), count($this->dmsTable->getDmsColumns()));
    }
}
