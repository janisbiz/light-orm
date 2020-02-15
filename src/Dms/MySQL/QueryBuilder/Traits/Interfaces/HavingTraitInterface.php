<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\TraitsInterface;

interface HavingTraitInterface
{
    /**
     * @param string $condition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function having($condition, array $bind = []);
}
