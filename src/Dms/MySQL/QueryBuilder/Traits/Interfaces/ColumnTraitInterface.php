<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\TraitsInterface;

interface ColumnTraitInterface
{
    /**
     * @param array|string $column
     * @param boolean $clearAll
     *
     * @return $this|TraitsInterface
     */
    public function column($column, $clearAll = false);
}
