<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\TraitsInterface;

trait CommandTrait
{
    /**
     * @var string
     */
    protected $command;

    /**
     * @param string $command
     *
     * @return $this|TraitsInterface
     */
    public function command($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * @return string
     */
    public function commandData()
    {
        return $this->command;
    }

    /**
     * @return string
     */
    protected function buildCommandQueryPart()
    {
        return $this->command;
    }
}
