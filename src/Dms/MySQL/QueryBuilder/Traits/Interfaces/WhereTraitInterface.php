<?php declare(strict_types=1);

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
    public function where(string $condition, array $bind = []);

    /**
     * @param string $column
     * @param array $params
     *
     * @return $this|TraitsInterface
     */
    public function whereIn(string $column, array $params = []);

    /**
     * @param string $column
     * @param array $params
     *
     * @return $this|TraitsInterface
     */
    public function whereNotIn(string $column, array $params = []);
}
