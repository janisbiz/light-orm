<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;

trait HavingTrait
{
    protected array $having = [];

    /**
     * @throws QueryBuilderException
     */
    public function having(string $condition, array $bind = []): static
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

    protected function buildHavingQueryPart(): ?string
    {
        return empty($this->having)
            ? null
            : sprintf('%s %s', ConditionEnum::HAVING, implode(' AND ', array_unique($this->having)))
        ;
    }
}
