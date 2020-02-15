<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\TraitsInterface;

trait BindTrait
{
    /**
     * @var array
     */
    protected $bind = [];

    /**
     * @param array $bind
     *
     * @return $this|TraitsInterface
     */
    public function bind(array $bind = [])
    {
        $this->bind = \array_merge($this->bind, $bind);

        return $this;
    }

    /**
     * @return array
     */
    public function bindData()
    {
        return $this->bind;
    }
}
