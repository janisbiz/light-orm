<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderInterface;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\TraitsInterface;

interface UnionTraitInterface
{
    /**
     * @param QueryBuilderInterface $queryBuilder
     *
     * @return $this|TraitsInterface
     */
    public function unionAll(QueryBuilderInterface $queryBuilder);
}
