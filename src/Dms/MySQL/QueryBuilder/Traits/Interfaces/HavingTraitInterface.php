<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface HavingTraitInterface
{
    public function having(string $condition, array $bind = []): static;
}
