<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface GroupByTraitInterface
{
    /**
     * @param array|string $groupBy
     *
     * @return $this
     */
    public function groupBy($groupBy);
}
