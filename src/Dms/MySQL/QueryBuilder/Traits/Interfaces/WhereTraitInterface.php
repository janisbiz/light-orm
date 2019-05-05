<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface WhereTraitInterface
{
    /**
     * @param string $condition
     * @param array $bind
     *
     * @return $this
     */
    public function where($condition, array $bind = []);

    /**
     * @param string $column
     * @param array $params
     *
     * @return $this
     */
    public function whereIn($column, array $params = []);

    /**
     * @param string $column
     * @param array $params
     *
     * @return $this
     */
    public function whereNotIn($column, array $params = []);
}
