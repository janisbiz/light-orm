<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface CommandTraitInterface
{
    /**
     * @param string $command
     *
     * @return $this
     */
    public function command(string $command);

    /**
     * @return string
     */
    public function commandData(): string;
}
