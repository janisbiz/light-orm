<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\Generator;

use Janisbiz\LightOrm\Dms\MySQL\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Dms\DmsDatabase;
use Janisbiz\LightOrm\Dms\MySQL\Generator\DmsFactory;
use Janisbiz\LightOrm\Generator\Dms\DmsDatabaseInterface;
use Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\Generator\Dms\DmsColumnTest;
use Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\Generator\Dms\DmsDatabaseTest;
use Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\Generator\Dms\DmsTableTest;
use PHPUnit\Framework\TestCase;

class DmsFactoryTest extends TestCase
{
    public function testCreateDmsDatabase()
    {
        /** @var ConnectionInterface|\PHPUnit_Framework_MockObject_MockObject $connection */
        $connection = $this->createPartialMock(
            ConnectionInterface::class,
            [
                'query',
                'setSqlSafeUpdates',
                'unsetSqlSafeUpdates',
                'beginTransaction',
            ]
        );

        $connection
            ->expects($this->exactly(2))
            ->method('query')
            ->willReturnCallback(function ($query) {
                switch ($query) {
                    case 'SHOW TABLES':
                        return [
                            (object) [
                                \sprintf('Tables_in_%s', DmsDatabaseTest::DATABASE_NAME) => DmsTableTest::TABLE_NAME,
                            ],
                        ];

                    case false !== \preg_match('/^SHOW COLUMNS FROM (\w+)$/', $query):
                        $dmsColumns = [];
                        for ($i = 1; $i <= 3; $i++) {
                            $dmsColumns[] = (object) [
                                'Field' => DmsColumnTest::COLUMN_NAME,
                                'Type' => DmsColumnTest::COLUMN_TYPE,
                                'Null' => DmsColumnTest::COLUMN_NULLABLE,
                                'Key' => DmsColumnTest::COLUMN_KEY,
                                'Default' => DmsColumnTest::COLUMN_DEFAULT,
                                'Extra' => DmsColumnTest::COLUMN_EXTRA,
                            ];
                        }

                        return $dmsColumns;
                }

                throw new \Exception();
            })
        ;

        $dmsDatabase = (new DmsFactory())->createDmsDatabase(DmsDatabaseTest::DATABASE_NAME, $connection);

        $this->assertTrue($dmsDatabase instanceof DmsDatabaseInterface);
        $this->assertTrue($dmsDatabase instanceof DmsDatabase);
    }
}
