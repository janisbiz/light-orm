<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface HavingTraitInterface
{
    /**
     * @param string $condition
     * @param array $bind
     *
     * @return $this
     */
    public function having(string $condition, array $bind = []);
}
