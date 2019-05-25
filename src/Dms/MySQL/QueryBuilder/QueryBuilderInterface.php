<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder;

use Janisbiz\LightOrm\Entity\EntityInterface;
use Janisbiz\LightOrm\QueryBuilder\QueryBuilderInterface as BaseQueryBuilderInterface;

interface QueryBuilderInterface extends BaseQueryBuilderInterface, TraitsInterface
{
    /**
     * @param bool $toString
     *
     * @return string|EntityInterface
     */
    public function insert($toString = false);
    /**
     * @param bool $toString
     *
     * @return string|EntityInterface
     */
    public function insertIgnore($toString = false);
    /**
     * @param bool $toString
     *
     * @return string|EntityInterface
     */
    public function replace($toString = false);

    /**
     * @param bool $toString
     *
     * @return string|EntityInterface[]
     */
    public function find($toString = false);

    /**
     * @param bool $toString
     *
     * @return null|string|EntityInterface
     */
    public function findOne($toString = false);

    /**
     * @param bool $toString
     *
     * @return bool|string|EntityInterface
     */
    public function update($toString = false);

    /**
     * @param bool $toString
     *
     * @return bool|string|EntityInterface
     */
    public function updateIgnore($toString = false);

    /**
     * @param bool $toString
     *
     * @return string|bool
     */
    public function delete($toString = false);

    /**
     * @param bool $toString
     *
     * @return int
     */
    public function count($toString = false);
}
