<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

trait CommandTrait
{
    /**
     * @var string
     */
    protected $command;

    /**
     * @param string $command
     *
     * @return $this
     */
    protected function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }
}
