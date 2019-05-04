<?php

namespace Janisbiz\LightOrm\Dms\MySQL\Generator\Writer;

use Janisbiz\LightOrm\Generator\Writer\AbstractWriter;
use Janisbiz\LightOrm\Generator\Dms\DmsDatabase;
use Janisbiz\LightOrm\Generator\Dms\DmsTable;
use Janisbiz\Heredoc\HeredocTrait;

class RepositoryClassWriter extends AbstractWriter
{
    use HeredocTrait;

    const FILE_NAME_SUFFIX = 'Repository';

    /**
     * @param DmsDatabase $database
     * @param DmsTable $table
     * @param string $directory
     * @param array $existingFiles
     *
     * @return RepositoryClassWriter
     */
    public function write(DmsDatabase $database, DmsTable $table, $directory, array &$existingFiles)
    {
        $fileName = $this->generateFileName($table, $directory, '', self::FILE_NAME_SUFFIX);

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
        return /** @lang PHP */
            <<<PHP
<?php
namespace {$database->getPhpName()}\Repository;

use Janisbiz\LightOrm\Dms\Mysql\Repository\AbstractRepository;
use {$database->getPhpName()}\\{$table->getPhpName()};

class {$table->getPhpName()}{$this->heredoc(self::FILE_NAME_SUFFIX)} extends AbstractRepository
{
    /**
    * @return string
    */
    protected function getModelClass()
    {
        return {$table->getPhpName()}::class;
    }
}

PHP;
    }
}
