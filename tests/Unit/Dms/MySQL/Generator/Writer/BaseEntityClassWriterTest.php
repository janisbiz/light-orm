<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\Generator\Writer;

use Janisbiz\LightOrm\Dms\MySQL\Generator\Dms\DmsColumn;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Dms\DmsDatabase;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Dms\DmsTable;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Writer\BaseEntityClassWriter;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Writer\WriterConfig;
use Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\Generator\Dms\DmsColumnTest;
use Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\Generator\Dms\DmsDatabaseTest;
use Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\Generator\Dms\DmsTableTest;
use Janisbiz\LightOrm\Tests\Unit\Generator\Writer\FileTrait;
use PHPUnit\Framework\TestCase;

class BaseEntityClassWriterTest extends TestCase
{
    use FileTrait;

    const WRITER_CONFIG_DIRECTORY_VALUE = JANISBIZ_LIGHT_ORM_ROOT_DIR
        . 'var'
        . DIRECTORY_SEPARATOR
        . 'light-orm'
        . DIRECTORY_SEPARATOR
        . 'phpunit'
    ;
    const WRITER_CONFIG_NAMESPACE_VALUE = 'None\Existent\Namespace';
    const WRITER_CONFIG_CLASS_PREFIX_VALUE = 'Base';

    /**
     * @var WriterConfig
     */
    private $writerConfig;

    /**
     * @var BaseEntityClassWriter
     */
    protected $baseEntityClassWriter;

    /**
     * @var DmsDatabase
     */
    protected $dmsDatabase;

    /**
     * @var DmsTable
     */
    protected $dmsTable;

    /**
     * @var string
     */
    protected $dmsGeneratedDirectory;

    public function setUp()
    {
        $this->writerConfig = new WriterConfig(
            static::WRITER_CONFIG_DIRECTORY_VALUE,
            static::WRITER_CONFIG_NAMESPACE_VALUE,
            static::WRITER_CONFIG_CLASS_PREFIX_VALUE
        );

        $this->baseEntityClassWriter = new BaseEntityClassWriter($this->writerConfig);


        $dmsColumns = [];
        for ($i = 1; $i <= 3; $i++) {
            $dmsColumns[] = new DmsColumn(
                \sprintf('%s_%d', DmsColumnTest::COLUMN_NAME, $i),
                DmsColumnTest::COLUMN_TYPE,
                DmsColumnTest::COLUMN_NULLABLE,
                2 >= $i ? DmsColumnTest::COLUMN_KEY : '',
                DmsColumnTest::COLUMN_DEFAULT,
                1 === $i ? DmsColumnTest::COLUMN_EXTRA : null
            );
        }

        $this->dmsTable = new DmsTable(
            \sprintf('%s_%d', DmsTableTest::TABLE_NAME, $i),
            $dmsColumns
        );

        $this->dmsDatabase = new DmsDatabase(DmsDatabaseTest::DATABASE_NAME, [$this->dmsTable]);

        $this->dmsGeneratedDirectory = \implode(
            '',
            [
                $this->writerConfig->getDirectory(),
                DIRECTORY_SEPARATOR,
                $this->dmsDatabase->getPhpName(),
                DIRECTORY_SEPARATOR,
                $this->writerConfig->getClassPrefix(),
            ]
        );
    }

    public function testConstruct()
    {
        $getWriterConfigMethod = new \ReflectionMethod($this->baseEntityClassWriter, 'getWriterConfig');
        $getWriterConfigMethod->setAccessible(true);

        $this->assertTrue($getWriterConfigMethod->invoke($this->baseEntityClassWriter) instanceof $this->writerConfig);
    }

    /**
     * @param array $files
     */
    public function testWrite(array &$files = [])
    {
        $this->baseEntityClassWriter->write($this->dmsDatabase, $this->dmsTable, $files);

        $baseEntityFilePath = \implode(
            '',
            [
                $this->dmsGeneratedDirectory,
                DIRECTORY_SEPARATOR,
                \sprintf('%s%s.php', $this->writerConfig->getClassPrefix(), $this->dmsTable->getPhpName())
            ]
        );
        $this->assertFileExists($baseEntityFilePath);
        $this->assertEquals(
            /** @lang PHP */
            <<<PHP
<?php declare(strict_types=1);

namespace None\Existent\Namespace\DatabaseNameSnakeCase\Base;

use Janisbiz\LightOrm\Entity\BaseEntity;

class BaseTableNameSnakeCase4 extends BaseEntity
{
    const DATABASE_NAME = 'database_name_snake_case';
    const TABLE_NAME = 'database_name_snake_case.table_name_snake_case_4';
    
    const COLUMN_NAME_SNAKE_CASE_1 = 'name_snake_case_1';
    const COLUMN_NAME_SNAKE_CASE_2 = 'name_snake_case_2';
    const COLUMN_NAME_SNAKE_CASE_3 = 'name_snake_case_3';
    
    /**
     * @param bool \$isNew
     */
    public function __construct(\$isNew = true)
    {
        \$this->primaryKeys = [
            static::COLUMN_NAME_SNAKE_CASE_1,
            static::COLUMN_NAME_SNAKE_CASE_2,
        ];
        \$this->primaryKeysAutoIncrement = [
            static::COLUMN_NAME_SNAKE_CASE_1,
        ];
        \$this->columns = [
            static::COLUMN_NAME_SNAKE_CASE_1,
            static::COLUMN_NAME_SNAKE_CASE_2,
            static::COLUMN_NAME_SNAKE_CASE_3,
        ];
        
        \$this->isNew = \$isNew;
        if (empty(\$this->data)) {
            \$this->isNew = true;
        }
    }
    
    /**
     * @return null|string
     */
    public function getNameSnakeCase1()
    {
        return \$this->data['name_snake_case_1'];
    }
    
    /**
     * @param null|string \$nameSnakeCase1
     *
     * @return \$this
     */
    public function setNameSnakeCase1(\$nameSnakeCase1)
    {
        \$this->data['name_snake_case_1'] = \$nameSnakeCase1;

        return \$this;
    }


    /**
     * @return null|string
     */
    public function getNameSnakeCase2()
    {
        return \$this->data['name_snake_case_2'];
    }
    
    /**
     * @param null|string \$nameSnakeCase2
     *
     * @return \$this
     */
    public function setNameSnakeCase2(\$nameSnakeCase2)
    {
        \$this->data['name_snake_case_2'] = \$nameSnakeCase2;

        return \$this;
    }


    /**
     * @return null|string
     */
    public function getNameSnakeCase3()
    {
        return \$this->data['name_snake_case_3'];
    }
    
    /**
     * @param null|string \$nameSnakeCase3
     *
     * @return \$this
     */
    public function setNameSnakeCase3(\$nameSnakeCase3)
    {
        \$this->data['name_snake_case_3'] = \$nameSnakeCase3;

        return \$this;
    }
}

PHP
            ,
            \file_get_contents($baseEntityFilePath)
        );
    }

    public function testWriteWhenFileExists()
    {
        $files = $this->createTestFiles(
            [
                \sprintf('%s%s.php', $this->writerConfig->getClassPrefix(), $this->dmsTable->getPhpName())
            ],
            $this->dmsGeneratedDirectory
        );

        $this->testWrite($files);
        $this->assertCount(0, $files);
    }

    public function tearDown()
    {
        $this->removeDirectoryRecursive(\implode(
            '',
            [
                JANISBIZ_LIGHT_ORM_ROOT_DIR,
                'var',
                DIRECTORY_SEPARATOR,
                'light-orm',
            ]
        ));
    }
}
