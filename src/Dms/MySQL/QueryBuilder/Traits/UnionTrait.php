<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderInterface;

trait UnionTrait
{
    /**
     * @var array
     */
    protected $unionAll = [];

    /**
     * @param QueryBuilderInterface $queryBuilder
     *
     * @return $this
     */
    public function unionAll(QueryBuilderInterface $queryBuilder)
    {
        $this->unionAll[] = \sprintf('(%s)', $queryBuilder->find(true));

        return $this;
    }
}
