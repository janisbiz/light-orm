<?php

namespace Janisbiz\LightOrm\MySQL\Traits;

use Janisbiz\LightOrm\BaseModel;
use Janisbiz\LightOrm\MySQL\Enums\CommandEnum;
use Janisbiz\LightOrm\MySQL\QueryBuilder;

trait UnionTrait
{
    public $unionAll = [];

    /**
     * @param BaseModel $object
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
