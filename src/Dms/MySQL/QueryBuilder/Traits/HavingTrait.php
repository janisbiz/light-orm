<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;

trait HavingTrait
{
    /**
     * @var array
     */
    protected $having = [];

    /**
     * @param string $condition
     * @param array $bind
     *
     * @return $this
     * @throws \Exception
     */
    public function having($condition, array $bind = [])
    {
        if (!$condition) {
            throw new \Exception('You must pass $condition to having function!');
        }

        $this->having[] = $condition;

        if (!empty($bind)) {
            $this->bind($bind);
        }

        return $this;
    }

    /**
     * @return null|string
     */
    protected function buildHavingQueryPart()
    {
        return empty($this->having)
            ? null
            : \sprintf('%s %s', ConditionEnum::HAVING, \implode(' AND ', \array_unique($this->having)))
        ;
    }
}
