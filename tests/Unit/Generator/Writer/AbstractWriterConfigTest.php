<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Generator\Writer;

use Janisbiz\LightOrm\Generator\Writer\AbstractWriterConfig;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

#[AllowMockObjectsWithoutExpectations]
class AbstractWriterConfigTest extends TestCase
{
    const string DIRECTORY_VALUE = JANISBIZ_LIGHT_ORM_ROOT_DIR
        . 'var'
        . DIRECTORY_SEPARATOR
        . 'light-orm'
        . DIRECTORY_SEPARATOR
        . 'phpunit'
        . DIRECTORY_SEPARATOR
    ;
    const string DIRECTORY_VALUE_EXPECTED = JANISBIZ_LIGHT_ORM_ROOT_DIR
        . 'var'
        . DIRECTORY_SEPARATOR
        . 'light-orm'
        . DIRECTORY_SEPARATOR
        . 'phpunit'
    ;

    const string NAMESPACE_VALUE = 'This\Is\A\Namespace\\';
    const string NAMESPACE_VALUE_EXPECTED = 'This\Is\A\Namespace';

    const string CLASS_PREFIX_VALUE = ' classPrefix ';
    const string CLASS_PREFIX_VALUE_EXPECTED = 'Classprefix';

    const string CLASS_SUFFIX_VALUE = ' classSuffix ';
    const string CLASS_SUFFIX_VALUE_EXPECTED = 'Classsuffix';

    private AbstractWriterConfig $abstractWriterConfig;

    public function setUp(): void
    {
        $this->abstractWriterConfig = $this->createPartialMock(AbstractWriterConfig::class, []);
        $abstractWriterConfigReflection = new ReflectionClass($this->abstractWriterConfig);

        $directoryProperty = $abstractWriterConfigReflection->getProperty('directory');
        $directoryProperty->setValue($this->abstractWriterConfig, static::DIRECTORY_VALUE);

        $namespaceProperty = $abstractWriterConfigReflection->getProperty('namespace');
        $namespaceProperty->setValue($this->abstractWriterConfig, static::NAMESPACE_VALUE);

        $classPrefixProperty = $abstractWriterConfigReflection->getProperty('classPrefix');
        $classPrefixProperty->setValue($this->abstractWriterConfig, static::CLASS_PREFIX_VALUE);

        $classSuffixProperty = $abstractWriterConfigReflection->getProperty('classSuffix');
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
