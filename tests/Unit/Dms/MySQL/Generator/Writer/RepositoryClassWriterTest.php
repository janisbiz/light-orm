<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\Generator\Writer;

use Janisbiz\LightOrm\Dms\MySQL\Generator\Writer\EntityClassWriter;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Writer\RepositoryClassWriter;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Writer\WriterConfig;
use ReflectionMethod;
use ReflectionProperty;

class RepositoryClassWriterTest extends EntityClassWriterTest
{
    const string WRITER_CONFIG_CLASS_PREFIX_VALUE = '';
    const string WRITER_CONFIG_CLASS_SUFFIX_VALUE = 'Repository';

    private WriterConfig $writerConfig;

    private RepositoryClassWriter $repositoryClassWriter;

    public function setUp(): void
    {
        parent::setUp();

        $this->writerConfig = new WriterConfig(
            static::WRITER_CONFIG_DIRECTORY_VALUE,
            static::WRITER_CONFIG_NAMESPACE_VALUE,
            static::WRITER_CONFIG_CLASS_PREFIX_VALUE,
            static::WRITER_CONFIG_CLASS_SUFFIX_VALUE
        );

        $this->repositoryClassWriter = new RepositoryClassWriter($this->writerConfig, $this->entityClassWriter);

        $this->dmsGeneratedDirectory = implode(
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
        $getWriterConfigMethod = new ReflectionMethod($this->repositoryClassWriter, 'getWriterConfig');

        $baseEntityClassWriterProperty = new ReflectionProperty($this->repositoryClassWriter, 'entityClassWriter');

        $this->assertTrue($getWriterConfigMethod->invoke($this->repositoryClassWriter) instanceof $this->writerConfig);
        $this->assertTrue(
            $baseEntityClassWriterProperty->getValue($this->repositoryClassWriter) instanceof $this->entityClassWriter
        );
    }

    public function testWrite(array &$files = [])
    {
        $files = [];
        $this->repositoryClassWriter->write($this->dmsDatabase, $this->dmsTable, $files);

        $repositoryFilePath = implode(
            '',
            [
                $this->dmsGeneratedDirectory,
                DIRECTORY_SEPARATOR,
                sprintf('%s%s.php', $this->dmsTable->getPhpName(), $this->writerConfig->getClassSuffix())
            ]
        );
        $this->assertFileExists($repositoryFilePath);
        $this->assertEquals(
            /** @lang PHP */
            <<<PHP
<?php declare(strict_types=1);

namespace None\Existent\Namespace\DatabaseNameSnakeCase\Repository;

use Janisbiz\LightOrm\Dms\MySQL\Repository\AbstractRepository;
use None\Existent\Namespace\DatabaseNameSnakeCase\Repository\TableNameSnakeCase4Repository;

class TableNameSnakeCase4Repository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return TableNameSnakeCase4Repository::class;
    }
}

PHP
            ,
            file_get_contents($repositoryFilePath)
        );
    }

    public function testWriteWhenFileExists()
    {
        $files = $this->createTestFiles(
            [
                sprintf('%s%s.php', $this->dmsTable->getPhpName(), $this->writerConfig->getClassSuffix())
            ],
            $this->dmsGeneratedDirectory
        );

        $this->repositoryClassWriter->write($this->dmsDatabase, $this->dmsTable, $files);

        $repositoryFilePath = implode(
            '',
            [
                $this->dmsGeneratedDirectory,
                DIRECTORY_SEPARATOR,
                sprintf('%s%s.php', $this->dmsTable->getPhpName(), $this->writerConfig->getClassSuffix())
            ]
        );
        $this->assertFileExists($repositoryFilePath);
        $this->assertEquals('', file_get_contents($repositoryFilePath));
    }
}
