<?php

namespace Janisbiz\LightOrm\Generator\Writer;

use Janisbiz\LightOrm\Generator\Dms\Database;
use Janisbiz\LightOrm\Generator\Dms\Table;

interface WriterInterface
{
    /**
     * @param Database $database
     * @param Table $table
     * @param string $directory
     * @param array $existingFiles
     *
     * @return $this
     */
    public function write(Database $database, Table $table, $directory, array &$existingFiles);
}
