<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

use Janisbiz\LightOrm\Dms\MySQL\Enum\KeywordEnum;

interface OrderByTraitInterface
{
    public function orderBy(array|string $orderBy, string $keyword = KeywordEnum::ASC): static;
}
