<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface BindTraitInterface
{
    /**
     * @param array $bind
     *
     * @return $this
     */
    public function bind(array $bind = []);

    /**
     * @return array
     */
    public function bindData();
}
