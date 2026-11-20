<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface WhereTraitInterface
{
    public function where(string $condition, array $bind = []): static;

    public function whereIn(string $column, array $params = []): static;

    public function whereNotIn(string $column, array $params = []): static;
}
