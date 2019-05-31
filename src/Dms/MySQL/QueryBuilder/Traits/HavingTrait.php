<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;

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
     * @throws QueryBuilderException
     */
    public function having(string $condition, array $bind = [])
    {
        if (!$condition) {
            throw new QueryBuilderException('You must pass $condition to having function!');
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
    protected function buildHavingQueryPart(): ?string
    {
        return empty($this->having)
            ? null
            : \sprintf('%s %s', ConditionEnum::HAVING, \implode(' AND ', \array_unique($this->having)))
        ;
    }
}
