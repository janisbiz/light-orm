<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface ColumnTraitInterface
{
    public function column(array|string $column, bool $clearAll = false): static;
}
