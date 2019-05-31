<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface ColumnTraitInterface
{
    /**
     * @param array|string $column
     * @param boolean $clearAll
     *
     * @return $this
     */
    public function column($column, bool $clearAll = false);
}
