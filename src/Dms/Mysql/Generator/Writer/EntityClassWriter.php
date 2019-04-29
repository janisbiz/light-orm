<?php

namespace Janisbiz\LightOrm\Dms\MySQL\Generator\Writer;

use Janisbiz\LightOrm\Generator\Writer\AbstractWriter;
use Janisbiz\LightOrm\Generator\Dms\Database;
use Janisbiz\LightOrm\Generator\Dms\Table;

class EntityClassWriter extends AbstractWriter
{
    /**
     * @param Database $database
     * @param Table $table
     * @param string $directory
     * @param array $existingFiles
     *
     * @return EntityClassWriter
     */
    public function write(Database $database, Table $table, $directory, array &$existingFiles)
    {
        $fileName = $this->generateFileName($table, $directory);

        return $this
            ->writeFile($fileName, $this->generateFileContents($database, $table), true)
            ->removeFileFromExistingFiles($fileName, $existingFiles)
        ;
    }

    /**
     * @param Database $database
     * @param Table $table
     *
     * @return string
     */
    private function generateFileContents(Database $database, Table $table)
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
