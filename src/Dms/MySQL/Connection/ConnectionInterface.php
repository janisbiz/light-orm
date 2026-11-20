<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\Connection;

use Janisbiz\LightOrm\Connection\ConnectionInterface as BaseConnectionInterface;

interface ConnectionInterface extends BaseConnectionInterface
{
    public function setSqlSafeUpdates(): self;

    public function unsetSqlSafeUpdates(): self;
}
