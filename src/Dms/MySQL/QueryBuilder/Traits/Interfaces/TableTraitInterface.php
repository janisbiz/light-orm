<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\TraitsInterface;

interface TableTraitInterface
{
    /**
     * @param array|string $table
     * @param boolean $clearAll
     *
     * @return $this|TraitsInterface
     */
    public function table($table, $clearAll = false);
}
