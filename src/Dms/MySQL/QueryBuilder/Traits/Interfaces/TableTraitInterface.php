<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface TableTraitInterface
{
    /**
     * @param array|string $table
     * @param boolean $clearAll
     *
     * @return $this
     */
    public function table($table, $clearAll = false);
}
