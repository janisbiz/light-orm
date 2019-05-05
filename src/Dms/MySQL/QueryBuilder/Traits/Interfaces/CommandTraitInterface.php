<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface CommandTraitInterface
{
    /**
     * @param string $command
     *
     * @return $this
     */
    public function command($command);
}
