<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\Enum\KeywordEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;

trait OrderByTrait
{
    protected array $orderBy = [];

    /**
     * @throws QueryBuilderException
     */
    public function orderBy(string|array|null $orderBy, string $keyword = KeywordEnum::ASC): static
    {
        if (empty($orderBy)) {
            throw new QueryBuilderException('You must pass $orderBy to orderBy method!');
        }

        if (!in_array($keyword, [KeywordEnum::ASC, KeywordEnum::DESC])) {
            throw new QueryBuilderException(sprintf('Invalid $keyword "%s" for orderBy!', $keyword));
        }

        if (!is_array($orderBy)) {
            $orderBy = [$orderBy];
        }

        foreach ($orderBy as $orderByColumn) {
            $this->orderBy[] = sprintf('%s %s', $orderByColumn, $keyword);
        }

        return $this;
    }

    protected function buildOrderByQueryPart(): ?string
    {
        return empty($this->orderBy)
            ? null
            : sprintf('%s %s', ConditionEnum::ORDER_BY, implode(', ', $this->orderBy))
        ;
    }
}
