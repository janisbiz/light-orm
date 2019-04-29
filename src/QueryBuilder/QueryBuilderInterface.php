<?php

namespace Janisbiz\LightOrm\QueryBuilder;

use Janisbiz\LightOrm\Entity\EntityInterface;

interface QueryBuilderInterface
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
     * @return string|bool
     */
    public function delete($toString = false);

    /**
     * @return string
     */
    public function buildQuery();

    /**
     * @return null|EntityInterface
     */
    public function getEntity();

    /**
     * @return string
     */
    public function toString();
}
