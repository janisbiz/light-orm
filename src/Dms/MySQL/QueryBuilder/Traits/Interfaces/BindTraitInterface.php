<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface BindTraitInterface
{
    public function bind(array $bind = []): static;

    /**
     * @return array
     */
    public function bindData(): array;
}
