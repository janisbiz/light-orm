<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\Generator\Writer;

use Janisbiz\LightOrm\Dms\MySQL\Generator\Writer\EntityClassWriter;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Writer\WriterConfig;

class EntityClassWriterTest extends BaseEntityClassWriterTest
{
    const WRITER_CONFIG_CLASS_PREFIX_VALUE = '';
    const WRITER_CONFIG_CLASS_SUFFIX_VALUE = 'Entity';

    /**
     * @var WriterConfig
     */
    private $writerConfig;

    /**
     * @var EntityClassWriter
     */
    protected $entityClassWriter;

    public function setUp()
    {
        parent::setUp();

        $this->writerConfig = new WriterConfig(
            static::WRITER_CONFIG_DIRECTORY_VALUE,
            static::WRITER_CONFIG_NAMESPACE_VALUE,
            static::WRITER_CONFIG_CLASS_PREFIX_VALUE,
            static::WRITER_CONFIG_CLASS_SUFFIX_VALUE
        );

        $this->entityClassWriter = new EntityClassWriter($this->writerConfig, $this->baseEntityClassWriter);

        $this->dmsGeneratedDirectory = \implode(
            '',
            [
                $this->writerConfig->getDirectory(),
                DIRECTORY_SEPARATOR,
                $this->dmsDatabase->getPhpName(),
                DIRECTORY_SEPARATOR,
                $this->writerConfig->getClassSuffix(),
            ]
        );
    }

    public function testConstruct()
    {
        $getWriterConfigMethod = new \ReflectionMethod($this->entityClassWriter, 'getWriterConfig');
        $getWriterConfigMethod->setAccessible(true);

        $baseEntityClassWriterProperty = new \ReflectionProperty($this->entityClassWriter, 'baseEntityClassWriter');
        $baseEntityClassWriterProperty->setAccessible(true);

        $this->assertTrue($getWriterConfigMethod->invoke($this->entityClassWriter) instanceof $this->writerConfig);
        $this->assertTrue(
            $baseEntityClassWriterProperty->getValue($this->entityClassWriter) instanceof $this->baseEntityClassWriter
        );
    }

    /**
     * @param array $files
     */
    public function testWrite(array &$files = [])
    {
        $this->entityClassWriter->write($this->dmsDatabase, $this->dmsTable, $files);

        $entityFilePath = \implode(
            '',
            [
                $this->dmsGeneratedDirectory,
                DIRECTORY_SEPARATOR,
                \sprintf('%s%s.php', $this->dmsTable->getPhpName(), $this->writerConfig->getClassSuffix())
            ]
        );
        $this->assertFileExists($entityFilePath);
        $this->assertEquals(
            /** @lang PHP */
            <<<PHP
<?php declare(strict_types=1);

namespace None\Existent\Namespace\DatabaseNameSnakeCase\Entity;

use None\Existent\Namespace\DatabaseNameSnakeCase\TableNameSnakeCase4;

class TableNameSnakeCase4Entity extends TableNameSnakeCase4
{
}

PHP
            ,
            \file_get_contents($entityFilePath)
        );
    }

    public function testWriteWhenFileExists()
    {
        $files = $this->createTestFiles(
            [
                \sprintf('%s%s.php', $this->dmsTable->getPhpName(), $this->writerConfig->getClassSuffix())
            ],
            $this->dmsGeneratedDirectory
        );

        $this->entityClassWriter->write($this->dmsDatabase, $this->dmsTable, $files);

        $repositoryFilePath = \implode(
            '',
            [
                $this->dmsGeneratedDirectory,
                DIRECTORY_SEPARATOR,
                \sprintf('%s%s.php', $this->dmsTable->getPhpName(), $this->writerConfig->getClassSuffix())
            ]
        );
        $this->assertFileExists($repositoryFilePath);
        $this->assertEquals('', \file_get_contents($repositoryFilePath));
    }
}
