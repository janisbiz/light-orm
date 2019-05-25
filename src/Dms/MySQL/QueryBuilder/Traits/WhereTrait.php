<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;

trait WhereTrait
{
    /**
     * @var array
     */
    protected $where = [];

    /**
     * @param string $condition
     * @param array $bind
     *
     * @return $this
     * @throws QueryBuilderException
     */
    public function where($condition, array $bind = [])
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

    /**
     * @param string $column
     * @param array $params
     *
     * @return $this
     */
    public function whereIn($column, array $params = [])
    {
        if (!empty($params)) {
            $bind = [];
            $bindParams = [];
            foreach ($params as $i => $param) {
                $bindParamName = \sprintf('%d_%s_In', $i, \str_replace('.', '', $column));

                $bind[$bindParamName] = $param;
                $bindParams[] = \sprintf(':%s', $bindParamName);
            }

            $this->where(
                \sprintf('%s IN (%s)', $column, \implode(', ', $bindParams)),
                $bind
            );
        }

        return $this;
    }

    /**
     * @param string $column
     * @param array $params
     *
     * @return $this
     */
    public function whereNotIn($column, array $params = [])
    {
        if (!empty($params)) {
            $bind = [];
            $bindParams = [];
            foreach ($params as $i => $param) {
                $bindParamName = \sprintf('%d_%s_NotIn', $i, \str_replace('.', '', $column));

                $bind[$bindParamName] = $param;
                $bindParams[] = \sprintf(':%s', $bindParamName);
            }

            $this->where(
                \sprintf('%s NOT IN (%s)', $column, \implode(', ', $bindParams)),
                $bind
            );
        }

        return $this;
    }

    /**
     * @return null|string
     */
    protected function buildWhereQueryPart()
    {
        return empty($this->where)
            ? null
            : \sprintf('%s %s', ConditionEnum::WHERE, \implode(' AND ', \array_unique($this->where)))
        ;
    }
}
