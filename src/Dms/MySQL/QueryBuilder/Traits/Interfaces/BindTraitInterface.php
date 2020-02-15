<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\TraitsInterface;

interface BindTraitInterface
{
    /**
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function bind(array $bind = []);

    /**
     * @return array
     */
    public function bindData(): array;
}
