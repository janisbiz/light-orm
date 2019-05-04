<?php

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
    public function createDmsDatabase($databaseName, ConnectionInterface $connection);
}
