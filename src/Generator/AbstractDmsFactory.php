<?php

namespace Janisbiz\LightOrm\Generator;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Generator\Dms\DmsColumnInterface;
use Janisbiz\LightOrm\Generator\Dms\DmsTableInterface;

abstract class AbstractDmsFactory implements DmsFactoryInterface
{
    /**
     * @param string $tableName
     * @param ConnectionInterface $connection
     *
     * @return DmsTableInterface
     */
    abstract protected function createDmsTable($tableName, ConnectionInterface $connection);

    /**
     * @param string $name
     * @param string $type
     * @param bool $nullable
     * @param string $key
     * @param string $default
     * @param null|string $extra
     *
     * @return DmsColumnInterface
     */
    abstract protected function createDmsColumn($name, $type, $nullable, $key, $default, $extra);
}
