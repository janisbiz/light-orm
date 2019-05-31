<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\Connection;

use Janisbiz\LightOrm\Connection\ConnectionInterface as BaseConnectionInterface;

interface ConnectionInterface extends BaseConnectionInterface
{
    /**
     * @return $this
     */
    public function setSqlSafeUpdates();

    /**
     * @return $this
     */
    public function unsetSqlSafeUpdates();
}
