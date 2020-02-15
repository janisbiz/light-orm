<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\TraitsInterface;

interface OnDuplicateKeyUpdateTraitInterface
{
    /**
     * @param string $column
     * @param null|int|string|double $value
     *
     * @return $this|TraitsInterface
     */
    public function onDuplicateKeyUpdate(string $column, $value);
}
