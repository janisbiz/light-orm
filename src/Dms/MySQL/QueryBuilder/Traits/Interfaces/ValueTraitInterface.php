<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\TraitsInterface;

interface ValueTraitInterface
{
    /**
     * @param string $column
     * @param null|int|string|double $value
     *
     * @return $this|TraitsInterface
     */
    public function value($column, $value);

    /**
     * @param array $bindValue
     *
     * @return $this|TraitsInterface
     */
    public function bindValue(array $bindValue = []);

    /**
     * @return array
     */
    public function bindValueData();
}
