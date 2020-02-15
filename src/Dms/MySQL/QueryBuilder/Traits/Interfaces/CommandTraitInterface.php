<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\TraitsInterface;

interface CommandTraitInterface
{
    /**
     * @param string $command
     *
     * @return $this|TraitsInterface
     */
    public function command(string $command);

    /**
     * @return string
     */
    public function commandData(): string;
}
