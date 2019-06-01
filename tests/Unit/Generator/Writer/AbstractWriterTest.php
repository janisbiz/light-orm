<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Generator\Writer;

use Janisbiz\LightOrm\Generator\Dms\DmsDatabaseInterface;
use Janisbiz\LightOrm\Generator\Dms\DmsTableInterface;
use Janisbiz\LightOrm\Generator\Writer\AbstractWriter;
use Janisbiz\LightOrm\Generator\Writer\AbstractWriterConfig;
use PHPUnit\Framework\TestCase;

class AbstractWriterTest extends TestCase
{
    use FileTrait;

    /**
     * @var AbstractWriter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $abstractWriter;

    /**
     * @var AbstractWriterConfig|\PHPUnit_Framework_MockObject_MockObject
     */
    private $abstractWriterConfig;

    /**
     * @var DmsDatabaseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dmsDatabase;

    /**
     * @var DmsTableInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dmsTable;

    public function setUp()
    {
        $this->abstractWriter = $this->getMockForAbstractClass(AbstractWriter::class);

        $this->abstractWriterConfig = $this->getMockForAbstractClass(AbstractWriterConfig::class);
        $abstractWriterConfigReflection = new \ReflectionClass($this->abstractWriterConfig);

        $directoryProperty = $abstractWriterConfigReflection->getProperty('directory');
        $directoryProperty->setAccessible(true);
        $directoryProperty->setValue($this->abstractWriterConfig, AbstractWriterConfigTest::DIRECTORY_VALUE);

        $namespaceProperty = $abstractWriterConfigReflection->getProperty('namespace');
        $namespaceProperty->setAccessible(true);
        $namespaceProperty->setValue($this->abstractWriterConfig, AbstractWriterConfigTest::NAMESPACE_VALUE);

        $classPrefixProperty = $abstractWriterConfigReflection->getProperty('classPrefix');
        $classPrefixProperty->setAccessible(true);
        $classPrefixProperty->setValue($this->abstractWriterConfig, AbstractWriterConfigTest::CLASS_PREFIX_VALUE);

        $classSuffixProperty = $abstractWriterConfigReflection->getProperty('classSuffix');
        $classSuffixProperty->setAccessible(true);
        $classSuffixProperty->setValue($this->abstractWriterConfig, AbstractWriterConfigTest::CLASS_SUFFIX_VALUE);
        
        $this->abstractWriter->method('getWriterConfig')->willReturn($this->abstractWriterConfig);

        $this->dmsDatabase = $this->createMock(DmsDatabaseInterface::class);
        $this->dmsDatabase->method('getPhpName')->willReturn('DmsDatabasePhpName');

        $this->dmsTable = $this->createMock(DmsTableInterface::class);
        $this->dmsTable->method('getPhpName')->willReturn('DmsTablePhpName');
    }

    public function testGenerateNamespace()
    {
        $this->assertEquals(
            \sprintf(
                '%s\\%s\\%s%s',
                AbstractWriterConfigTest::NAMESPACE_VALUE_EXPECTED,
                $this->dmsDatabase->getPhpName(),
                AbstractWriterConfigTest::CLASS_PREFIX_VALUE_EXPECTED,
                AbstractWriterConfigTest::CLASS_SUFFIX_VALUE_EXPECTED
            ),
            $this->abstractWriter->generateNamespace($this->dmsDatabase)
        );
    }

    public function testGenerateClassName()
    {
        $this->assertEquals(
            \implode(
                '',
                [
                    AbstractWriterConfigTest::CLASS_PREFIX_VALUE_EXPECTED,
                    $this->dmsTable->getPhpName(),
                    AbstractWriterConfigTest::CLASS_SUFFIX_VALUE_EXPECTED,
                ]
            ),
            $this->abstractWriter->generateClassName($this->dmsTable)
        );
    }

    public function testGenerateFQDN()
    {
        $this->assertEquals(
            \sprintf(
                '%s\\%s\\%s%s\\%s%s%s',
                AbstractWriterConfigTest::NAMESPACE_VALUE_EXPECTED,
                $this->dmsDatabase->getPhpName(),
                AbstractWriterConfigTest::CLASS_PREFIX_VALUE_EXPECTED,
                AbstractWriterConfigTest::CLASS_SUFFIX_VALUE_EXPECTED,
                AbstractWriterConfigTest::CLASS_PREFIX_VALUE_EXPECTED,
                $this->dmsTable->getPhpName(),
                AbstractWriterConfigTest::CLASS_SUFFIX_VALUE_EXPECTED
            ),
            $this->abstractWriter->generateFQDN($this->dmsDatabase, $this->dmsTable)
        );
    }
    
    public function testRead()
    {
        $expectedFiles = $this->createTestFiles(
            [
                'FileOne.php',
                'FileTwo.php',
                'FileThree.php',
            ],
            \implode('', [
                $this->abstractWriterConfig->getDirectory(),
                DIRECTORY_SEPARATOR,
                $this->dmsDatabase->getPhpName(),
                DIRECTORY_SEPARATOR,
                $this->abstractWriterConfig->getClassPrefix(),
                $this->abstractWriterConfig->getClassSuffix(),
            ])
        );

        $files = $this->abstractWriter->read($this->dmsDatabase);

        $this->assertCount(\count($expectedFiles), $files);
        $this->assertEquals($expectedFiles, \array_keys($files));
    }

    public function testReadWhenDirectoryDoesNotExist()
    {
        $files = $this->abstractWriter->read($this->dmsDatabase);

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
