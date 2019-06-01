<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\QueryBuilder;

use Janisbiz\LightOrm\Entity\EntityInterface;

interface QueryBuilderInterface
{
    /**
     * @return string
     */
    public function buildQuery(): string;

    /**
     * @return null|EntityInterface
     */
    public function getEntity(): ?EntityInterface;

    /**
     * @return string
     */
    public function toString(): string;

    /**
     * @param bool $toString
     *
     * @return int
     */
    public function count(bool $toString = false);
}
