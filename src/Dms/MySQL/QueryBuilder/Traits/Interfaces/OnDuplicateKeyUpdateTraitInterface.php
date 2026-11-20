<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface OnDuplicateKeyUpdateTraitInterface
{
    public function onDuplicateKeyUpdate(string $column, string|int|float|null $value): static;
}
