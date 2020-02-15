<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\TraitsInterface;

trait GroupByTrait
{
    /**
     * @var array
     */
    protected $groupBy = [];

    /**
     * @param array|string $groupBy
     *
     * @return $this|TraitsInterface
     * @throws QueryBuilderException
     */
    public function groupBy($groupBy)
    {
        if (empty($groupBy)) {
            throw new QueryBuilderException('You must pass $groupBy to groupBy method!');
        }

        if (!\is_array($groupBy)) {
            $groupBy = [$groupBy];
        }

        $this->groupBy = \array_merge($this->groupBy, $groupBy);

        return $this;
    }

    /**
     * @return null|string
     */
    protected function buildGroupByQueryPart()
    {
        return empty($this->groupBy)
            ? null
            : \sprintf('%s %s', ConditionEnum::GROUP_BY, \implode(', ', $this->groupBy))
        ;
    }
}
