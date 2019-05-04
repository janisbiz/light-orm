<?php

namespace Janisbiz\LightOrm\Generator\Writer;

use Janisbiz\LightOrm\Generator\Dms\DmsDatabaseInterface;
use Janisbiz\LightOrm\Generator\Dms\DmsTableInterface;

interface WriterInterface
{
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
