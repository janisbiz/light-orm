<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface ValueTraitInterface
{
    /**
     * @param string $column
     * @param null|int|string|double $value
     *
     * @return $this
     */
    public function value(string $column, $value);

    /**
     * @param array $bindValue
     *
     * @return $this
     */
    public function bindValue(array $bindValue = []);

    /**
     * @return array
     */
    public function bindValueData(): array;
}
