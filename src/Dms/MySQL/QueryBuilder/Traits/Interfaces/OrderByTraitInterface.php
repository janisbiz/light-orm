<?php declare(strict_types=1);

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
    public function orderBy($orderBy, string $keyword = KeywordEnum::ASC);
}
