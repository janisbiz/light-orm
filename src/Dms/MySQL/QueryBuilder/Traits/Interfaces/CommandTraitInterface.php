<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface CommandTraitInterface
{
    public function command(string $command): static;

    public function commandData(): string;
}
