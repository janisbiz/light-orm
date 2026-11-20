<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface GroupByTraitInterface
{
    public function groupBy(array|string $groupBy): static;
}
