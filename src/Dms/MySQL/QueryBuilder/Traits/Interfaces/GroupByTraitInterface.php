<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\TraitsInterface;

interface GroupByTraitInterface
{
    /**
     * @param array|string $groupBy
     *
     * @return $this|TraitsInterface
     */
    public function groupBy($groupBy);
}
