<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Generator;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Generator\AbstractDmsFactory;
use Janisbiz\LightOrm\Generator\Dms\DmsDatabaseInterface;
use PHPUnit\Framework\TestCase;

class AbstractDmsFactoryTest extends TestCase
{
    /**
     * @var AbstractDmsFactory
     */
    private $abstractDmsFactory;

    public function setUp()
    {
        $this->abstractDmsFactory = $this->getMockForAbstractClass(AbstractDmsFactory::class);
        $this->abstractDmsFactory
            ->expects($this->any())
            ->method('createDmsDatabase')
            ->willReturn($this->createMock(DmsDatabaseInterface::class))
        ;
    }

    public function testCreateDmsDatabase()
    {
        /** @var ConnectionInterface $connection */
        $connection = $this->createMock(ConnectionInterface::class);
        $dmsDatabase = $this->abstractDmsFactory->createDmsDatabase('test', $connection);

        $this->assertTrue($dmsDatabase instanceof DmsDatabaseInterface);
    }
}
