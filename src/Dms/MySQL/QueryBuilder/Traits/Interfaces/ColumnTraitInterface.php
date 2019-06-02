<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface ColumnTraitInterface
{
    /**
     * @param array|string $column
     * @param boolean $clearAll
     *
     * @return $this
     */
    public function column($column, $clearAll = false);
}
