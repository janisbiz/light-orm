<?php

namespace Janisbiz\LightOrm\Dms\Mysql\Repository;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder;
use Janisbiz\LightOrm\Entity\EntityInterface;
use Janisbiz\LightOrm\Repository\AbstractRepository as BaseAbstractRepository;

abstract class AbstractRepository extends BaseAbstractRepository
{
    /**
     * @param QueryBuilder $queryBuilder
     * @param bool $toString
     *
     * @throws \Exception
     * @return string|EntityInterface
     */
    public function insertIgnore(QueryBuilder $queryBuilder, $toString = false)
    {
        return $this->insert($queryBuilder, $toString);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param bool $toString
     *
     * @throws \Exception
     * @return string|EntityInterface
     */
    public function replace(QueryBuilder $queryBuilder, $toString = false)
    {
        return $this->insert($queryBuilder, $toString);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param bool $toString
     *
     * @return null|string|bool|EntityInterface
     */
    public function updateIgnore(QueryBuilder $queryBuilder, $toString = false)
    {
        return $this->update($queryBuilder, $toString);
    }
}
