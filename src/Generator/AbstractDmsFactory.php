<?php

namespace Janisbiz\LightOrm\Generator;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Generator\Dms\DmsColumn;
use Janisbiz\LightOrm\Generator\Dms\DmsTable;

abstract class AbstractDmsFactory implements DmsFactoryInterface
{
    /**
     * @param string $tableName
     * @param ConnectionInterface $connection
     *
     * @return DmsTable
     */
    abstract protected function createTable($tableName, ConnectionInterface $connection);

    /**
     * @param string $name
     * @param string $type
     * @param bool $nullable
     * @param string $key
     * @param string $default
     * @param null|string $extra
     *
     * @return DmsColumn
     */
    abstract protected function createColumn($name, $type, $nullable, $key, $default, $extra);
}
