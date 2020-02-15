<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\TraitsInterface;

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
     * @return $this|TraitsInterface
     * @throws QueryBuilderException
     */
    public function where(string $condition, array $bind = [])
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
     * @return $this|TraitsInterface
     */
    public function whereIn(string $column, array $params = [])
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
     * @return $this|TraitsInterface
     */
    public function whereNotIn(string $column, array $params = [])
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
    protected function buildWhereQueryPart(): ?string
    {
        return empty($this->where)
            ? null
            : \sprintf('%s %s', ConditionEnum::WHERE, \implode(' AND ', \array_unique($this->where)))
        ;
    }
}
