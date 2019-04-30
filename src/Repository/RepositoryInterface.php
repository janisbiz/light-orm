<?php

namespace Janisbiz\LightOrm\Repository;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilder;
use Janisbiz\LightOrm\Entity\EntityInterface;

interface RepositoryInterface
{
    /**
     * @param QueryBuilder $queryBuilder
     * @param bool $toString
     *
     * @throws \Exception
     * @return string|EntityInterface
     */
    public function insert(QueryBuilder $queryBuilder, $toString = false);

    /**
     * @param QueryBuilder $queryBuilder
     * @param bool $toString
     *
     * @return null|string|EntityInterface
     */
    public function findOne(QueryBuilder $queryBuilder, $toString = false);

    /**
     * @param QueryBuilder $queryBuilder
     * @param bool $toString
     *
     * @return string|array
     */
    public function find(QueryBuilder $queryBuilder, $toString = false);

    /**
     * @param QueryBuilder $queryBuilder
     * @param bool $toString
     *
     * @return null|string|bool|EntityInterface
     */
    public function update(QueryBuilder $queryBuilder, $toString = false);

    /**
     * @param QueryBuilder $queryBuilder
     * @param bool $toString
     *
     * @return string|bool
     */
    public function delete(QueryBuilder $queryBuilder, $toString = false);
}
