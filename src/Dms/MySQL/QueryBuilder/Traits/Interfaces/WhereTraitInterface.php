<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\TraitsInterface;

interface WhereTraitInterface
{
    /**
     * @param string $condition
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function where($condition, array $bind = []);

    /**
     * @param string $column
     * @param array $params
     *
     * @return $this|TraitsInterface
     */
    public function whereIn($column, array $params = []);

    /**
     * @param string $column
     * @param array $params
     *
     * @return $this|TraitsInterface
     */
    public function whereNotIn($column, array $params = []);
}
