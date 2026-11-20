<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

trait CommandTrait
{
    protected string $command;

    public function command(string $command): static
    {
        $this->command = $command;

        return $this;
    }

    public function commandData(): string
    {
        return $this->command;
    }

    protected function buildCommandQueryPart(): string
    {
        return $this->command;
    }
}
