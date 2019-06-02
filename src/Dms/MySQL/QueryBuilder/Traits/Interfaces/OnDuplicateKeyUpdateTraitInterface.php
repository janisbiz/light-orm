<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface OnDuplicateKeyUpdateTraitInterface
{
    /**
     * @param string $column
     * @param null|int|string|double $value
     *
     * @return $this
     */
    public function onDuplicateKeyUpdate($column, $value);
}
