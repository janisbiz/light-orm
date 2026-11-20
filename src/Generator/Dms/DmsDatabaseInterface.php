<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Generator\Dms;

interface DmsDatabaseInterface
{
    public function getName(): string;

    public function getPhpName(): string;

    /**
     * @return DmsTableInterface[]
     */
    public function getDmsTables(): array;
}
