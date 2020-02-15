<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\JoinEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\TraitsInterface;

trait JoinTrait
{
    /**
     * @var array
     */
    protected $join = [];

    /**
     * @param string $join
     * @param string $tableName
     * @param string $onCondition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     * @throws QueryBuilderException
     */
    public function join(string $join, string $tableName, string $onCondition, array $bind = [])
    {
        if (!\in_array($join, JoinEnum::JOINS)) {
            throw new QueryBuilderException(\sprintf('$join "%s" is not a valid join type', $join));
        }

        if (empty($tableName)) {
            throw new QueryBuilderException('You must pass $table name to join method!');
        }

        if (empty($onCondition)) {
            throw new QueryBuilderException('You must pass $onCondition name to join method!');
        }

        $joinString = \sprintf('%s %s ON (%s)', $join, $tableName, $onCondition);

        if (\array_search($joinString, $this->join, false) === false) {
            $this->join[] = $joinString;
        }

        if (!empty($bind)) {
            $this->bind($bind);
        }

        return $this;
    }

    /**
     * @param string $join
     * @param string $tableName
     * @param string $alias
     * @param string $onCondition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     * @throws QueryBuilderException
     */
    public function joinAs(string $join, string $tableName, string $alias, string $onCondition, array $bind = [])
    {
        if (empty($alias)) {
            throw new QueryBuilderException('You must pass $alias name to join method!');
        }

        return $this->join($join, \sprintf('%s AS %s', $tableName, $alias), $onCondition, $bind);
    }

    /**
     * @param string $tableName
     * @param string $onCondition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function innerJoin(string $tableName, string $onCondition, array $bind = [])
    {
        return $this->join(JoinEnum::INNER_JOIN, $tableName, $onCondition, $bind);
    }

    /**
     * @param string $tableName
     * @param string $onCondition
     * @param string $alias
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function innerJoinAs(string $tableName, string $alias, string $onCondition, array $bind = [])
    {
        return $this->joinAs(JoinEnum::INNER_JOIN, $tableName, $alias, $onCondition, $bind);
    }

    /**
     * @param string $tableName
     * @param string $onCondition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function leftJoin(string $tableName, string $onCondition, array $bind = [])
    {
        return $this->join(JoinEnum::LEFT_JOIN, $tableName, $onCondition, $bind);
    }

    /**
     * @param string $tableName
     * @param string $onCondition
     * @param string $alias
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function leftJoinAs(string $tableName, string $alias, string $onCondition, array $bind = [])
    {
        return $this->joinAs(JoinEnum::LEFT_JOIN, $tableName, $alias, $onCondition, $bind);
    }

    /**
     * @param string $tableName
     * @param string $onCondition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function rightJoin(string $tableName, string $onCondition, array $bind = [])
    {
        return $this->join(JoinEnum::RIGHT_JOIN, $tableName, $onCondition, $bind);
    }

    /**
     * @param string $tableName
     * @param string $alias
     * @param string $onCondition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function rightJoinAs(string $tableName, string $alias, string $onCondition, array $bind = [])
    {
        return $this->joinAs(JoinEnum::RIGHT_JOIN, $tableName, $alias, $onCondition, $bind);
    }

    /**
     * @param string $tableName
     * @param string $onCondition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function crossJoin(string $tableName, string $onCondition, array $bind = [])
    {
        return $this->join(JoinEnum::CROSS_JOIN, $tableName, $onCondition, $bind);
    }

    /**
     * @param string $tableName
     * @param string $onCondition
     * @param string $alias
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function crossJoinAs(string $tableName, string $alias, string $onCondition, array $bind = [])
    {
        return $this->joinAs(JoinEnum::CROSS_JOIN, $tableName, $alias, $onCondition, $bind);
    }

    /**
     * @param string $tableName
     * @param string $onCondition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function fullOuterJoin(string $tableName, string $onCondition, array $bind = [])
    {
        return $this->join(JoinEnum::FULL_OUTER_JOIN, $tableName, $onCondition, $bind);
    }

    /**
     * @param string $tableName
     * @param string $alias
     * @param string $onCondition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function fullOuterJoinAs(string $tableName, string $alias, string $onCondition, array $bind = [])
    {
        return $this->joinAs(JoinEnum::FULL_OUTER_JOIN, $tableName, $alias, $onCondition, $bind);
    }

    /**
     * @return null|string
     */
    protected function buildJoinQueryPart(): ?string
    {
        return empty($this->join) ? null : \implode(' ', $this->join);
    }
}
