<?php

namespace Janisbiz\LightOrm\MySQL\Generator\Writer;

use Janisbiz\LightOrm\MySQL\Generator\Database;
use Janisbiz\LightOrm\MySQL\Generator\Table;

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
