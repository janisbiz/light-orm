<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface ValueTraitInterface
{
    public function value(string $column, string|int|float|null $value): static;

    public function bindValue(array $bindValue = []): static;

    public function bindValueData(): array;
}
