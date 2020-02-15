<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\TraitsInterface;

interface JoinTraitInterface
{
    /**
     * @param string $join
     * @param string $tableName
     * @param string $onCondition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function join($join, $tableName, $onCondition, array $bind = []);

    /**
     * @param string $join
     * @param string $tableName
     * @param string $alias
     * @param string $onCondition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function joinAs($join, $tableName, $alias, $onCondition, array $bind = []);

    /**
     * @param string $tableName
     * @param string $onCondition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function innerJoin($tableName, $onCondition, array $bind = []);

    /**
     * @param string $tableName
     * @param string $onCondition
     * @param string $alias
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function innerJoinAs($tableName, $alias, $onCondition, array $bind = []);

    /**
     * @param string $tableName
     * @param string $onCondition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function leftJoin($tableName, $onCondition, array $bind = []);

    /**
     * @param string $tableName
     * @param string $onCondition
     * @param string $alias
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function leftJoinAs($tableName, $alias, $onCondition, array $bind = []);

    /**
     * @param string $tableName
     * @param string $onCondition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function rightJoin($tableName, $onCondition, array $bind = []);

    /**
     * @param string $tableName
     * @param string $alias
     * @param string $onCondition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function rightJoinAs($tableName, $alias, $onCondition, array $bind = []);

    /**
     * @param string $tableName
     * @param string $onCondition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function crossJoin($tableName, $onCondition, array $bind = []);

    /**
     * @param string $tableName
     * @param string $onCondition
     * @param string $alias
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function crossJoinAs($tableName, $alias, $onCondition, array $bind = []);

    /**
     * @param string $tableName
     * @param string $onCondition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function fullOuterJoin($tableName, $onCondition, array $bind = []);

    /**
     * @param string $tableName
     * @param string $alias
     * @param string $onCondition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function fullOuterJoinAs($tableName, $alias, $onCondition, array $bind = []);
}
