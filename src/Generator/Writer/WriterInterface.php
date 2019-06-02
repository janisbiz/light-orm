<?php

namespace Janisbiz\LightOrm\Generator\Writer;

use Janisbiz\LightOrm\Generator\Dms\DmsDatabaseInterface;
use Janisbiz\LightOrm\Generator\Dms\DmsTableInterface;

interface WriterInterface
{
    const CLASS_CONSTANT_DATABASE_NAME = 'DATABASE_NAME';
    const CLASS_CONSTANT_TABLE_NAME = 'TABLE_NAME';

    /**
     * @param DmsDatabaseInterface $dmsDatabase
     * @param DmsTableInterface $dmsTable
     * @param array $existingFiles
     *
     * @return $this
     */
    public function write(
        DmsDatabaseInterface $dmsDatabase,
        DmsTableInterface $dmsTable,
        array &$existingFiles
    );

    /**
     * @param DmsDatabaseInterface $dmsDatabase
     *
     * @return string[]
     */
    public function read(DmsDatabaseInterface $dmsDatabase);
}
