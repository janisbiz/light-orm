<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface FromTraitInterface
{
    /**
     * @param array|string $from
     * @param boolean $clearAll
     *
     * @return $this
     */
    public function from($from, $clearAll = false);
}
