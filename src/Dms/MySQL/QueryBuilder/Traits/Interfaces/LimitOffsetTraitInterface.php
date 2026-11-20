<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface LimitOffsetTraitInterface
{
    public function limitWithOffset(int $limit, int $offset): static;

    public function offset(int $offset): static;

    public function limit(int $limit): static;
}
