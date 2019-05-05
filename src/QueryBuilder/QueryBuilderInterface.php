<?php

namespace Janisbiz\LightOrm\QueryBuilder;

use Janisbiz\LightOrm\Entity\EntityInterface;

interface QueryBuilderInterface
{
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
