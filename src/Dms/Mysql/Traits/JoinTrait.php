<?php

namespace Janisbiz\LightOrm\Dms\MySQL\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\JoinEnum;

trait JoinTrait
{
    public $join = [];

    /**
     * @param string $tableName
     * @param string $condition
     * @param array $bind
     *
     * @return $this
     */
    public function innerJoin($tableName, $condition, array $bind = [])
    {
        return $this->join(JoinEnum::INNER, $tableName, $condition, $bind);
    }

    /**
     * @param string $join
     * @param string $tableName
     * @param string $condition
     * @param array $bind
     *
     * @return $this
     * @throws \Exception
     */
    public function join($join, $tableName, $condition, array $bind = [])
    {
        if (!\in_array($join, JoinEnum::JOINS)) {
            throw new \Exception(\sprintf('$join "%s" is not a valid join type', $join));
        }

        if (empty($tableName)) {
            throw new \Exception('You must pass $table name to join method!');
        }

        if (empty($condition)) {
            throw new \Exception('You must pass $condition name to join method!');
        }

        $joinString = \sprintf(
            '%s %s ON (%s)',
            $join,
            $tableName,
            $condition
        );

        if (array_search($joinString, $this->join, false) === false) {
            $this->join[] = $joinString;
        }

        if (!empty($bind)) {
            $this->bind($bind);
        }

        return $this;
    }

    /**
     * @param string $tableName
     * @param string $condition
     * @param string $alias
     * @param array $bind
     *
     * @return $this
     */
    public function innerJoinAs($tableName, $alias, $condition, array $bind = [])
    {
        return $this->join(JoinEnum::INNER, \sprintf('%s AS %s', $tableName, $alias), $condition, $bind);
    }

    /**
     * @param string $tableName
     * @param string $condition
     * @param array $bind
     *
     * @return $this
     */
    public function leftJoin($tableName, $condition, array $bind = [])
    {
        return $this->join(JoinEnum::LEFT, $tableName, $condition, $bind);
    }

    /**
     * @param string $tableName
     * @param string $condition
     * @param string $alias
     * @param array $bind
     *
     * @return $this
     */
    public function leftJoinAs($tableName, $alias, $condition, array $bind = [])
    {
        return $this->join(JoinEnum::LEFT, \sprintf('%s AS %s', $tableName, $alias), $condition, $bind);
    }

    /**
     * @param string $tableName
     * @param string $condition
     * @param array $bind
     *
     * @return $this
     */
    public function rightJoin($tableName, $condition, array $bind = [])
    {
        return $this->join(JoinEnum::RIGHT, $tableName, $condition, $bind);
    }

    /**
     * @param string $tableName
     * @param string $condition
     * @param string $alias
     * @param array $bind
     *
     * @return $this
     */
    public function rightJoinAs($tableName, $condition, $alias, array $bind = [])
    {
        return $this->join(JoinEnum::RIGHT, \sprintf('%s AS %s', $tableName, $alias), $condition, $bind);
    }

    /**
     * @param string $tableName
     * @param string $condition
     * @param array $bind
     *
     * @return $this
     */
    public function crossJoin($tableName, $condition, array $bind = [])
    {
        return $this->join(JoinEnum::CROSS, $tableName, $condition, $bind);
    }

    /**
     * @param string $tableName
     * @param string $condition
     * @param string $alias
     * @param array $bind
     *
     * @return $this
     */
    public function crossJoinAs($tableName, $alias, $condition, array $bind = [])
    {
        return $this->join(JoinEnum::CROSS, \sprintf('%s AS %s', $tableName, $alias), $condition, $bind);
    }

    /**
     * @param string $tableName
     * @param string $condition
     * @param array $bind
     *
     * @return $this
     */
    public function fullOuterJoin($tableName, $condition, array $bind = [])
    {
        return $this->join(JoinEnum::FULL_OUTER, $tableName, $condition, $bind);
    }

    /**
     * @param string $tableName
     * @param string $condition
     * @param string $alias
     * @param array $bind
     *
     * @return $this
     */
    public function fullOuterJoinAs($tableName, $alias, $condition, array $bind = [])
    {
        return $this->join(JoinEnum::FULL_OUTER, \sprintf('%s AS %s', $tableName, $alias), $condition, $bind);
    }
}
