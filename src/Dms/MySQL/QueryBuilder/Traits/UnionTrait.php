<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Entity\BaseEntity;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilder;

trait UnionTrait
{
    /**
     * @var array
     */
    protected $unionAll = [];

    /**
     * @param QueryBuilder $queryBuilder
     *
     * @return $this
     */
    public function unionAll(QueryBuilder $queryBuilder)
    {
        $this->unionAll[] = \sprintf('(%s)', $queryBuilder->find(true));

        return $this;
    }
}
