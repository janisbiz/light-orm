<?php

namespace Janisbiz\LightOrm\Generator;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Dms\DmsDatabase;

interface DmsFactoryInterface
{
    /**
     * @param string $databaseName
     * @param ConnectionInterface $connection
     *
     * @return DmsDatabase
     */
    public function createDmsDatabase($databaseName, ConnectionInterface $connection);
}
