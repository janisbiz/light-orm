<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Generator;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Generator\Dms\DmsDatabaseInterface;

interface DmsFactoryInterface
{
    /**
     * @param string $databaseName
     * @param ConnectionInterface $connection
     *
     * @return DmsDatabaseInterface
     */
    public function createDmsDatabase(string $databaseName, ConnectionInterface $connection): DmsDatabaseInterface;
}
