<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

use Janisbiz\LightOrm\Dms\MySQL\Enum\KeywordEnum;

interface OrderByTraitInterface
{
    /**
     * @param string|array $orderBy
     * @param string $keyword
     *
     * @return $this
     */
    public function orderBy($orderBy, string $keyword = KeywordEnum::ASC);
}
