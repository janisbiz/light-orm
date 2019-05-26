<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\Generator\Writer;

use Janisbiz\LightOrm\Dms\MySQL\Generator\Writer\EntityClassWriter;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Writer\RepositoryClassWriter;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Writer\WriterConfig;

class RepositoryClassWriterTest extends EntityClassWriterTest
{
    const WRITER_CONFIG_CLASS_PREFIX_VALUE = '';
    const WRITER_CONFIG_CLASS_SUFFIX_VALUE = 'Repository';

    /**
     * @var WriterConfig
     */
    private $writerConfig;

    /**
     * @var EntityClassWriter
     */
    private $repositoryClassWriter;

    public function setUp()
    {
        parent::setUp();

        $this->writerConfig = new WriterConfig(
            static::WRITER_CONFIG_DIRECTORY_VALUE,
            static::WRITER_CONFIG_NAMESPACE_VALUE,
            static::WRITER_CONFIG_CLASS_PREFIX_VALUE,
            static::WRITER_CONFIG_CLASS_SUFFIX_VALUE
        );

        $this->repositoryClassWriter = new RepositoryClassWriter($this->writerConfig, $this->entityClassWriter);

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
        $getWriterConfigMethod = new \ReflectionMethod($this->repositoryClassWriter, 'getWriterConfig');
        $getWriterConfigMethod->setAccessible(true);

        $baseEntityClassWriterProperty = new \ReflectionProperty($this->repositoryClassWriter, 'entityClassWriter');
        $baseEntityClassWriterProperty->setAccessible(true);

        $this->assertTrue($getWriterConfigMethod->invoke($this->repositoryClassWriter) instanceof $this->writerConfig);
        $this->assertTrue(
            $baseEntityClassWriterProperty->getValue($this->repositoryClassWriter) instanceof $this->entityClassWriter
        );
    }

    /**
     * @param array $files
     */
    public function testWrite(array &$files = [])
    {
        $files = [];
        $this->repositoryClassWriter->write($this->dmsDatabase, $this->dmsTable, $files);

        $repositoryFilePath = \implode(
            '',
            [
                $this->dmsGeneratedDirectory,
                DIRECTORY_SEPARATOR,
                \sprintf('%s%s.php', $this->dmsTable->getPhpName(), $this->writerConfig->getClassSuffix())
            ]
        );
        $this->assertFileExists($repositoryFilePath);
        $this->assertEquals(
            /** @lang PHP */
            <<<PHP
<?php

namespace None\Existent\Namespace\DatabaseNameSnakeCase\Repository;

use Janisbiz\LightOrm\Dms\MySQL\Repository\AbstractRepository;
use None\Existent\Namespace\DatabaseNameSnakeCase\Repository\TableNameSnakeCase4Repository;

class TableNameSnakeCase4Repository extends AbstractRepository
{
    /**
    * @return string
    */
    protected function getModelClass()
    {
        return TableNameSnakeCase4Repository::class;
    }
}

PHP
            ,
            \file_get_contents($repositoryFilePath)
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

        $this->repositoryClassWriter->write($this->dmsDatabase, $this->dmsTable, $files);

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
