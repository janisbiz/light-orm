<?php

namespace Janisbiz\LightOrm\Generator;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Generator\Dms\Database;

interface GeneratorFactoryInterface
{
    /**
     * @param string $databaseName
     * @param ConnectionInterface $connection
     *
     * @return Database
     */
    public function createDatabase($databaseName, ConnectionInterface $connection);
}
