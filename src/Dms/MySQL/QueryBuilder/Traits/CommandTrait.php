<?php declare(strict_types=1);

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
    public function command(string $command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * @return string
     */
    public function commandData(): string
    {
        return $this->command;
    }

    /**
     * @return string
     */
    protected function buildCommandQueryPart(): string
    {
        return $this->command;
    }
}
