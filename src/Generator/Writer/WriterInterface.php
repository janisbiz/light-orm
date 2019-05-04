<?php

namespace Janisbiz\LightOrm\Generator\Writer;

use Janisbiz\LightOrm\Generator\Dms\DmsDatabase;
use Janisbiz\LightOrm\Generator\Dms\DmsTable;

interface WriterInterface
{
    /**
     * @param DmsDatabase $database
     * @param DmsTable $table
     * @param string $directory
     * @param array $existingFiles
     *
     * @return $this
     */
    public function write(DmsDatabase $database, DmsTable $table, $directory, array &$existingFiles);
}
