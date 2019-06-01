<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface OnDuplicateKeyUpdateTraitInterface
{
    /**
     * @param string $column
     * @param null|int|string|double $value
     *
     * @return $this
     */
    public function onDuplicateKeyUpdate(string $column, $value);
}
