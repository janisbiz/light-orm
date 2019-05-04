<?php

namespace Janisbiz\LightOrm\Dms\MySQL\Generator\Writer;

use Janisbiz\LightOrm\Generator\Writer\AbstractWriter;
use Janisbiz\LightOrm\Generator\Dms\DmsDatabase;
use Janisbiz\LightOrm\Generator\Dms\DmsTable;

class EntityClassWriter extends AbstractWriter
{
    /**
     * @param DmsDatabase $database
     * @param DmsTable $table
     * @param string $directory
     * @param array $existingFiles
     *
     * @return EntityClassWriter
     */
    public function write(DmsDatabase $database, DmsTable $table, $directory, array &$existingFiles)
    {
        $fileName = $this->generateFileName($table, $directory);

        return $this
            ->writeFile($fileName, $this->generateFileContents($database, $table), true)
            ->removeFileFromExistingFiles($fileName, $existingFiles)
        ;
    }

    /**
     * @param DmsDatabase $database
     * @param DmsTable $table
     *
     * @return string
     */
    private function generateFileContents(DmsDatabase $database, DmsTable $table)
    {
        return /** @lang PHP */<<<PHP
<?php
namespace {$database->getPhpName()};

class {$table->getPhpName()} extends Base\Base{$table->getPhpName()}
{
}

PHP;
    }
}
