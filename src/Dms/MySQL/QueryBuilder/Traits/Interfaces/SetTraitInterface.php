<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface SetTraitInterface
{
    public function set(string $column, string|int|float|null $value): static;
}
