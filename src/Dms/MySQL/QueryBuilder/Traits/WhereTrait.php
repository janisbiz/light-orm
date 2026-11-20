<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;

trait WhereTrait
{
    protected array $where = [];

    /**
     * @throws QueryBuilderException
     */
    public function where(string $condition, array $bind = []): static
    {
        if (empty($condition)) {
            throw new QueryBuilderException('You must pass $condition name to where method!');
        }

        $this->where[] = $condition;

        if (!empty($bind)) {
            $this->bind($bind);
        }

        return $this;
    }

    public function whereIn(string $column, array $params = []): static
    {
        if (!empty($params)) {
            $bind = [];
            $bindParams = [];
            foreach ($params as $i => $param) {
                $bindParamName = sprintf('%d_%s_In', $i, str_replace('.', '', $column));

                $bind[$bindParamName] = $param;
                $bindParams[] = sprintf(':%s', $bindParamName);
            }

            $this->where(
                sprintf('%s IN (%s)', $column, implode(', ', $bindParams)),
                $bind
            );
        }

        return $this;
    }

    public function whereNotIn(string $column, array $params = []): static
    {
        if (!empty($params)) {
            $bind = [];
            $bindParams = [];
            foreach ($params as $i => $param) {
                $bindParamName = sprintf('%d_%s_NotIn', $i, str_replace('.', '', $column));

                $bind[$bindParamName] = $param;
                $bindParams[] = sprintf(':%s', $bindParamName);
            }

            $this->where(
                sprintf('%s NOT IN (%s)', $column, implode(', ', $bindParams)),
                $bind
            );
        }

        return $this;
    }

    protected function buildWhereQueryPart(): ?string
    {
        return empty($this->where)
            ? null
            : sprintf('%s %s', ConditionEnum::WHERE, implode(' AND ', array_unique($this->where)))
        ;
    }
}
