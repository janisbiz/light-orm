<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface TableTraitInterface
{
    /**
     * @param array|string $table
     * @param bool $clearAll
     *
     * @return $this
     */
    public function table($table, bool $clearAll = false);
}
