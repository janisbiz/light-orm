<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

trait GroupByTrait
{
    /**
     * @var array
     */
    protected $groupBy = [];

    /**
     * @param array|string $groupBy
     *
     * @return $this
     * @throws \Exception
     */
    public function groupBy($groupBy)
    {
        if (empty($groupBy)) {
            throw new \Exception('You must pass $groupBy to groupBy method!');
        }

        if (!\is_array($groupBy)) {
            $groupBy = [$groupBy];
        }

        $this->groupBy = \array_merge($this->groupBy, $groupBy);

        return $this;
    }
}
