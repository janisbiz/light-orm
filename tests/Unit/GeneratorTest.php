<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Dms\MySQL\Generator\DmsFactory;
use Janisbiz\LightOrm\Generator;
use Janisbiz\LightOrm\Generator\Dms\DmsDatabaseInterface;
use Janisbiz\LightOrm\Generator\Dms\DmsTableInterface;
use Janisbiz\LightOrm\Generator\Writer\WriterInterface;
use Janisbiz\LightOrm\Tests\Unit\Generator\Writer\AbstractWriterConfigTest;
use Janisbiz\LightOrm\Tests\Unit\Generator\Writer\FileTrait;
use PHPUnit\Framework\TestCase;

class GeneratorTest extends TestCase
{
    use FileTrait;

    /**
     * @var WriterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $writer;

    /**
     * @var DmsTableInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dmsTable;

    /**
     * @var DmsDatabaseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dmsDatabase;

    /**
     * @var DmsFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dmsFactory;

    /**
     * @var Generator
     */
    private $generator;

    public function setUp()
    {
        $this->writer = $this->createMock(WriterInterface::class);

        $this->dmsTable = $this->createMock(DmsTableInterface::class);

        $this->dmsDatabase = $this->createMock(DmsDatabaseInterface::class);
        $this->dmsDatabase->method('getDmsTables')->willReturn([
            $this->dmsTable,
            $this->dmsTable,
            $this->dmsTable,
        ]);

        $this->dmsFactory = $this->createMock(DmsFactory::class);
        $this->dmsFactory->method('createDmsDatabase')->willReturn($this->dmsDatabase);

        $this->generator = new Generator($this->dmsFactory);
    }

    public function testConstruct()
    {
        $dmsFactoryProperty = new \ReflectionProperty($this->generator, 'dmsFactory');
        $dmsFactoryProperty->setAccessible(true);

        $this->assertTrue($dmsFactoryProperty->getValue($this->generator) instanceof $this->dmsFactory);
    }

    public function testAddWriter()
    {
        $writersToSet = [
            $this->writer,
            $this->writer,
            $this->writer,
        ];

        foreach ($writersToSet as $writerToSet) {
            $this->generator->addWriter($writerToSet);
        }

        $writersProperty = new \ReflectionProperty($this->generator, 'writers');
        $writersProperty->setAccessible(true);

        $writers = $writersProperty->getValue($this->generator);

        $this->assertCount(1, $writers);

        foreach ($writers as $writerClass => $writer) {
            $this->assertEquals(\get_class($this->writer), $writerClass);
            $this->assertTrue($writer instanceof $this->writer);
        }
    }

    public function testGenerate()
    {
        $this->testAddWriter();

        $testFiles = \array_flip($this->createTestFiles(
            [
                'FileOne.php',
                'FileTwo.php',
                'FileThree.php',
            ],
            AbstractWriterConfigTest::DIRECTORY_VALUE
        ));

        \array_walk($testFiles, function (&$i, $path) {
            $i = \basename($path);
        });

        $this->writer->expects($this->once())->method('read')->willReturn($testFiles);
        $this->writer->expects($this->exactly(\count($this->dmsDatabase->getDmsTables())))->method('write');

        /** @var ConnectionInterface|\PHPUnit_Framework_MockObject_MockObject $connection */
        $connection = $this->createMock(ConnectionInterface::class);

        $this->generator->generate($connection, 'database_name');

        foreach (\array_keys($testFiles) as $testFilePath) {
            $this->assertFalse(\file_exists($testFilePath));
        }
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
