<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\Generator\Writer;

use Janisbiz\LightOrm\Dms\MySQL\Generator\Writer\WriterConfig;
use Janisbiz\LightOrm\Tests\Unit\Generator\Writer\AbstractWriterConfigTest;
use PHPUnit\Framework\TestCase;

class WriterConfigTest extends TestCase
{
    public function testConstruct()
    {
        $writerConfig = new WriterConfig(
            AbstractWriterConfigTest::DIRECTORY_VALUE,
            AbstractWriterConfigTest::NAMESPACE_VALUE,
            AbstractWriterConfigTest::CLASS_PREFIX_VALUE,
            AbstractWriterConfigTest::CLASS_SUFFIX_VALUE
        );

        $this->assertEquals($writerConfig->getDirectory(), AbstractWriterConfigTest::DIRECTORY_VALUE_EXPECTED);
        $this->assertEquals($writerConfig->getNamespace(), AbstractWriterConfigTest::NAMESPACE_VALUE_EXPECTED);
        $this->assertEquals($writerConfig->getClassPrefix(), AbstractWriterConfigTest::CLASS_PREFIX_VALUE_EXPECTED);
        $this->assertEquals($writerConfig->getClassSuffix(), AbstractWriterConfigTest::CLASS_SUFFIX_VALUE_EXPECTED);
    }
}
