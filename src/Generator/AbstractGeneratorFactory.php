<?php

namespace Janisbiz\LightOrm\Generator;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Generator\Dms\Column;
use Janisbiz\LightOrm\Generator\Dms\Table;

abstract class AbstractGeneratorFactory implements GeneratorFactoryInterface
{
    /**
     * @param string $tableName
     * @param ConnectionInterface $connection
     *
     * @return Table
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
     * @return Column
     */
    abstract protected function createColumn($name, $type, $nullable, $key, $default, $extra);
}
