<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface TableTraitInterface
{
    public function table(array|string $table, bool $clearAll = false): static;
}
