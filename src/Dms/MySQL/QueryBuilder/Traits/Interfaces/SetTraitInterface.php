<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\TraitsInterface;

interface SetTraitInterface
{
    /**
     * @param string $column
     * @param null|int|string $value
     *
     * @return $this|TraitsInterface
     */
    public function set(string $column, $value);
}
