<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\TraitsInterface;

interface TableTraitInterface
{
    /**
     * @param array|string $table
     * @param bool $clearAll
     *
     * @return $this|TraitsInterface
     */
    public function table($table, bool $clearAll = false);
}
