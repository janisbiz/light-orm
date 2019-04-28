<?php

namespace Janisbiz\LightOrm\MySQL\Traits;

trait BindTrait
{
    public $bind = [];

    /**
     * @param array $bind
     *
     * @return $this
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
