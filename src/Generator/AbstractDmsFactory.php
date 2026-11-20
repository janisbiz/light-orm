<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Generator;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Generator\Dms\DmsColumnInterface;
use Janisbiz\LightOrm\Generator\Dms\DmsTableInterface;

abstract class AbstractDmsFactory implements DmsFactoryInterface
{
    abstract protected function createDmsTable(string $tableName, ConnectionInterface $connection): DmsTableInterface;

    abstract protected function createDmsColumn(
        string $name,
        string $type,
        bool $nullable,
        string $key,
        string $default,
        ?string $extra
    ): DmsColumnInterface;
}
