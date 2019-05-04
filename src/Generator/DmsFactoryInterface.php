<?php

namespace Janisbiz\LightOrm\Generator;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Generator\Dms\DmsDatabase;

interface DmsFactoryInterface
{
    /**
     * @param string $databaseName
     * @param ConnectionInterface $connection
     *
     * @return DmsDatabase
     */
    public function createDatabase($databaseName, ConnectionInterface $connection);
}
