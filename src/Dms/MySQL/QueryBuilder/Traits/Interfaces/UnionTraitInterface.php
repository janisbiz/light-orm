<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderInterface;

interface UnionTraitInterface
{
    /**
     * @param QueryBuilderInterface $queryBuilder
     *
     * @return $this
     */
    public function unionAll(QueryBuilderInterface $queryBuilder);
}
