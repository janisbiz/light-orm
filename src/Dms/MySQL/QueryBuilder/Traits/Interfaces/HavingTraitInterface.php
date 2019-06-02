<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface HavingTraitInterface
{
    /**
     * @param string $condition
     * @param array $bind
     *
     * @return $this
     */
    public function having($condition, array $bind = []);
}
