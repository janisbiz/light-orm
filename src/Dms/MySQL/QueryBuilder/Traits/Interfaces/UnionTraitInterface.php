<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderInterface;

interface UnionTraitInterface
{
    public function unionAll(QueryBuilderInterface $queryBuilder): static;
}
