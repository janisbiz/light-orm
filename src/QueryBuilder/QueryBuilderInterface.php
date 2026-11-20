<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\QueryBuilder;

use Janisbiz\LightOrm\Entity\EntityInterface;

interface QueryBuilderInterface
{
    public function buildQuery(): string;

    public function getEntity(): ?EntityInterface;

    public function toString(): string;

    public function count(bool $toString = false): string|int;
}
