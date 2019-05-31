<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Generator\Dms;

interface DmsTableInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getPhpName(): string;

    /**
     * @return DmsColumnInterface[]
     */
    public function getDmsColumns(): array;
}
