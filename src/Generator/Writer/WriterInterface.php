<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Generator\Writer;

use Janisbiz\LightOrm\Generator\Dms\DmsDatabaseInterface;
use Janisbiz\LightOrm\Generator\Dms\DmsTableInterface;

interface WriterInterface
{
    const string CLASS_CONSTANT_DATABASE_NAME = 'DATABASE_NAME';
    const string CLASS_CONSTANT_TABLE_NAME = 'TABLE_NAME';

    public function write(
        DmsDatabaseInterface $dmsDatabase,
        DmsTableInterface $dmsTable,
        array &$existingFiles
    ): static;

    /**
     * @return string[]
     */
    public function read(DmsDatabaseInterface $dmsDatabase): array;
}
