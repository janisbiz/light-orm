<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface SetTraitInterface
{
    /**
     * @param string $column
     * @param null|int|string $value
     *
     * @return $this
     */
    public function set(string $column, $value);
}
