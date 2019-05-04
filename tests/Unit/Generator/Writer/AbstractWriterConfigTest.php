<?php

namespace Janisbiz\LightOrm\Tests\Unit\Generator\Writer;

use Janisbiz\LightOrm\Generator\Writer\AbstractWriterConfig;
use PHPUnit\Framework\TestCase;

class AbstractWriterConfigTest extends TestCase
{
    const DIRECTORY_VALUE = '/path/to/directory' . DIRECTORY_SEPARATOR;
    const DIRECTORY_VALUE_EXPECTED = '/path/to/directory';

    const NAMESPACE_VALUE = 'This\Is\A\Namespace\\';
    const NAMESPACE_VALUE_EXPECTED = 'This\Is\A\Namespace';

    const CLASS_PREFIX_VALUE = ' classPrefix ';
    const CLASS_PREFIX_VALUE_EXPECTED = 'Classprefix';

    const CLASS_SUFFIX_VALUE = ' classSuffix ';
    const CLASS_SUFFIX_VALUE_EXPECTED = 'Classsuffix';

    /**
     * @var AbstractWriterConfig
     */
    private $abstractWriterConfig;

    public function setUp()
    {
        $this->abstractWriterConfig = $this->getMockForAbstractClass(AbstractWriterConfig::class);
        $abstractWriterConfigReflection = new \ReflectionClass($this->abstractWriterConfig);

        $directoryProperty = $abstractWriterConfigReflection->getProperty('directory');
        $directoryProperty->setAccessible(true);
        $directoryProperty->setValue($this->abstractWriterConfig, self::DIRECTORY_VALUE);

        $namespaceProperty = $abstractWriterConfigReflection->getProperty('namespace');
        $namespaceProperty->setAccessible(true);
        $namespaceProperty->setValue($this->abstractWriterConfig, self::NAMESPACE_VALUE);

        $classPrefixProperty = $abstractWriterConfigReflection->getProperty('classPrefix');
        $classPrefixProperty->setAccessible(true);
        $classPrefixProperty->setValue($this->abstractWriterConfig, self::CLASS_PREFIX_VALUE);

        $classSuffixProperty = $abstractWriterConfigReflection->getProperty('classSuffix');
        $classSuffixProperty->setAccessible(true);
        $classSuffixProperty->setValue($this->abstractWriterConfig, self::CLASS_SUFFIX_VALUE);
    }

    public function testGetDirectory()
    {
        $this->assertEquals($this->abstractWriterConfig->getDirectory(), self::DIRECTORY_VALUE_EXPECTED);
    }

    public function testGetNamespace()
    {
        $this->assertEquals($this->abstractWriterConfig->getNamespace(), self::NAMESPACE_VALUE_EXPECTED);
    }

    public function testGetClassPrefix()
    {
        $this->assertEquals($this->abstractWriterConfig->getClassPrefix(), self::CLASS_PREFIX_VALUE_EXPECTED);
    }

    public function testGetClassSuffix()
    {
        $this->assertEquals($this->abstractWriterConfig->getClassSuffix(), self::CLASS_SUFFIX_VALUE_EXPECTED);
    }
}
