<?php

namespace Janisbiz\LightOrm\Tests\Unit\Generator\Writer;

use Janisbiz\LightOrm\Generator\Dms\DmsDatabaseInterface;
use Janisbiz\LightOrm\Generator\Dms\DmsTableInterface;
use Janisbiz\LightOrm\Generator\Writer\AbstractWriter;
use Janisbiz\LightOrm\Generator\Writer\AbstractWriterConfig;
use PHPUnit\Framework\TestCase;

class AbstractWriterTest extends TestCase
{
    /**
     * @var AbstractWriter
     */
    private $abstractWriter;

    /**
     * @var AbstractWriterConfig
     */
    private $abstractWriterConfig;

    /**
     * @var DmsDatabaseInterface
     */
    private $dmsDatabaseInterface;

    /**
     * @var DmsTableInterface
     */
    private $dmsTableInterface;

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

        $this->dmsDatabaseInterface = $this->createMock(DmsDatabaseInterface::class);
        $this->dmsDatabaseInterface->method('getPhpName')->willReturn('DmsDatabasePhpName');

        $this->dmsTableInterface = $this->createMock(DmsTableInterface::class);
        $this->dmsTableInterface->method('getPhpName')->willReturn('DmsTablePhpName');
    }

    public function testGenerateNamespace()
    {
        $this->assertEquals(
            \sprintf(
                '%s\\%s\\%s%s',
                AbstractWriterConfigTest::NAMESPACE_VALUE_EXPECTED,
                $this->dmsDatabaseInterface->getPhpName(),
                AbstractWriterConfigTest::CLASS_PREFIX_VALUE_EXPECTED,
                AbstractWriterConfigTest::CLASS_SUFFIX_VALUE_EXPECTED
            ),
            $this->abstractWriter->generateNamespace($this->dmsDatabaseInterface)
        );
    }

    public function testGenerateClassName()
    {
        $this->assertEquals(
            \implode(
                '',
                [
                    AbstractWriterConfigTest::CLASS_PREFIX_VALUE_EXPECTED,
                    $this->dmsTableInterface->getPhpName(),
                    AbstractWriterConfigTest::CLASS_SUFFIX_VALUE_EXPECTED,
                ]
            ),
            $this->abstractWriter->generateClassName($this->dmsTableInterface)
        );
    }

    public function testGenerateFQDN()
    {
        $this->assertEquals(
            \sprintf(
                '%s\\%s\\%s%s\\%s%s%s',
                AbstractWriterConfigTest::NAMESPACE_VALUE_EXPECTED,
                $this->dmsDatabaseInterface->getPhpName(),
                AbstractWriterConfigTest::CLASS_PREFIX_VALUE_EXPECTED,
                AbstractWriterConfigTest::CLASS_SUFFIX_VALUE_EXPECTED,
                AbstractWriterConfigTest::CLASS_PREFIX_VALUE_EXPECTED,
                $this->dmsTableInterface->getPhpName(),
                AbstractWriterConfigTest::CLASS_SUFFIX_VALUE_EXPECTED
            ),
            $this->abstractWriter->generateFQDN($this->dmsDatabaseInterface, $this->dmsTableInterface)
        );
    }
}
