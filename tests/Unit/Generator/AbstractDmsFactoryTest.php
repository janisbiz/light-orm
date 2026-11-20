<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Generator;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Generator\AbstractDmsFactory;
use Janisbiz\LightOrm\Generator\Dms\DmsDatabaseInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class AbstractDmsFactoryTest extends TestCase
{
    private AbstractDmsFactory $abstractDmsFactory;

    public function setUp(): void
    {
        $this->abstractDmsFactory = $this->createPartialMock(
            AbstractDmsFactory::class,
            [
                'createDmsTable',
                'createDmsColumn',
                'createDmsDatabase',
            ],
        );
        $this->abstractDmsFactory
            ->expects($this->once())
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
