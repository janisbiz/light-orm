<?php

namespace Janisbiz\LightOrm\Repository;

use Janisbiz\LightOrm\QueryBuilder\QueryBuilderInterface;

interface RepositoryInterface
{
    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param bool $toString
     *
     * @return int
     */
    public function count(QueryBuilderInterface $queryBuilder, $toString = false);

    /**
     * @param null|double|int|string $value
     *
     * @return null
     */
    public function quote($value);
}
