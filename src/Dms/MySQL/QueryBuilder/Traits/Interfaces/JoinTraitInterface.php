<?php declare(strict_types=1);

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
    public function join(string $join, string $tableName, string $onCondition, array $bind = []);

    /**
     * @param string $join
     * @param string $tableName
     * @param string $alias
     * @param string $onCondition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function joinAs(string $join, string $tableName, string $alias, string $onCondition, array $bind = []);

    /**
     * @param string $tableName
     * @param string $onCondition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function innerJoin(string $tableName, string $onCondition, array $bind = []);

    /**
     * @param string $tableName
     * @param string $onCondition
     * @param string $alias
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function innerJoinAs(string $tableName, string $alias, string $onCondition, array $bind = []);

    /**
     * @param string $tableName
     * @param string $onCondition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function leftJoin(string $tableName, string $onCondition, array $bind = []);

    /**
     * @param string $tableName
     * @param string $onCondition
     * @param string $alias
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function leftJoinAs(string $tableName, string $alias, string $onCondition, array $bind = []);

    /**
     * @param string $tableName
     * @param string $onCondition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function rightJoin(string $tableName, string $onCondition, array $bind = []);

    /**
     * @param string $tableName
     * @param string $alias
     * @param string $onCondition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function rightJoinAs(string $tableName, string $alias, string $onCondition, array $bind = []);

    /**
     * @param string $tableName
     * @param string $onCondition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function crossJoin(string $tableName, string $onCondition, array $bind = []);

    /**
     * @param string $tableName
     * @param string $onCondition
     * @param string $alias
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function crossJoinAs(string $tableName, string $alias, string $onCondition, array $bind = []);

    /**
     * @param string $tableName
     * @param string $onCondition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function fullOuterJoin(string $tableName, string $onCondition, array $bind = []);

    /**
     * @param string $tableName
     * @param string $alias
     * @param string $onCondition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function fullOuterJoinAs(string $tableName, string $alias, string $onCondition, array $bind = []);
}
