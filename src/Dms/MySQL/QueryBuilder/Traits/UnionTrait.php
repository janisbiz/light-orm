<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Entity\BaseEntity;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilder;

trait UnionTrait
{
    public $unionAll = [];

    /**
     * @param BaseEntity $object
     *
     * @return $this
     * @throws \Exception
     */
    public function unionAll(QueryBuilder $queryBuilder)
    {
        $this->unionAll[] = \sprintf('(%s)', $queryBuilder->find(true));

        return $this;
    }
}