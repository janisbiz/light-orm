<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

use Janisbiz\LightOrm\Dms\MySQL\Enum\KeywordEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\TraitsInterface;

interface OrderByTraitInterface
{
    /**
     * @param string|array $orderBy
     * @param string $keyword
     *
     * @return $this|TraitsInterface
     */
    public function orderBy($orderBy, $keyword = KeywordEnum::ASC);
}
