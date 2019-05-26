<?php

namespace Janisbiz\LightOrm\Tests\Unit\Generator\Writer;

use Janisbiz\LightOrm\Generator\Writer\AbstractWriterConfig;
use PHPUnit\Framework\TestCase;

class AbstractWriterConfigTest extends TestCase
{
    const DIRECTORY_VALUE = JANISBIZ_LIGHT_ORM_ROOT_DIR
        . 'var'
        . DIRECTORY_SEPARATOR
        . 'light-orm'
        . DIRECTORY_SEPARATOR
        . 'phpunit'
        . DIRECTORY_SEPARATOR
    ;
    const DIRECTORY_VALUE_EXPECTED = JANISBIZ_LIGHT_ORM_ROOT_DIR
        . 'var'
        . DIRECTORY_SEPARATOR
        . 'light-orm'
        . DIRECTORY_SEPARATOR
        . 'phpunit'
    ;

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
        $directoryProperty->setValue($this->abstractWriterConfig, static::DIRECTORY_VALUE);

        $namespaceProperty = $abstractWriterConfigReflection->getProperty('namespace');
        $namespaceProperty->setAccessible(true);
        $namespaceProperty->setValue($this->abstractWriterConfig, static::NAMESPACE_VALUE);

        $classPrefixProperty = $abstractWriterConfigReflection->getProperty('classPrefix');
        $classPrefixProperty->setAccessible(true);
        $classPrefixProperty->setValue($this->abstractWriterConfig, static::CLASS_PREFIX_VALUE);

        $classSuffixProperty = $abstractWriterConfigReflection->getProperty('classSuffix');
        $classSuffixProperty->setAccessible(true);
        $classSuffixProperty->setValue($this->abstractWriterConfig, static::CLASS_SUFFIX_VALUE);
    }

    public function testGetDirectory()
    {
        $this->assertEquals($this->abstractWriterConfig->getDirectory(), static::DIRECTORY_VALUE_EXPECTED);
    }

    public function testGetNamespace()
    {
        $this->assertEquals($this->abstractWriterConfig->getNamespace(), static::NAMESPACE_VALUE_EXPECTED);
    }

    public function testGetClassPrefix()
    {
        $this->assertEquals($this->abstractWriterConfig->getClassPrefix(), static::CLASS_PREFIX_VALUE_EXPECTED);
    }

    public function testGetClassSuffix()
    {
        $this->assertEquals($this->abstractWriterConfig->getClassSuffix(), static::CLASS_SUFFIX_VALUE_EXPECTED);
    }
}
