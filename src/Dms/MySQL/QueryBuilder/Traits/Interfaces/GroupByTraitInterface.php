<?php declare(strict_types=1);

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
