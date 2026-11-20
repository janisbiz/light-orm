<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Generator\Dms;

interface DmsTableInterface
{
    public function getName(): string;

    public function getPhpName(): string;

    /**
     * @return DmsColumnInterface[]
     */
    public function getDmsColumns(): array;
}
