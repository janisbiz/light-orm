<?php

namespace Janisbiz\LightOrm\MySQL\Traits;

trait CommandTrait
{
    public $command;

    /**
     * @param string $command
     *
     * @return $this
     */
    private function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }
}
