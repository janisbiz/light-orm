<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\TraitsInterface;

interface CommandTraitInterface
{
    /**
     * @param string $command
     *
     * @return $this|TraitsInterface
     */
    public function command($command);

    /**
     * @return string
     */
    public function commandData();
}
