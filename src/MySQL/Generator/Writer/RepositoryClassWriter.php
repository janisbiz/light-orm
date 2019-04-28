<?php

namespace Janisbiz\LightOrm\MySQL\Generator\Writer;

use Janisbiz\LightOrm\MySQL\Generator\Database;
use Janisbiz\LightOrm\MySQL\Generator\Table;
use Janisbiz\Heredoc\HeredocTrait;

class RepositoryClassWriter extends AbstractWriter
{
    use HeredocTrait;

    const FILE_NAME_SUFFIX = 'Repository';

    /**
     * @param Database $database
     * @param Table $table
     * @param string $directory
     * @param array $existingFiles
     *
     * @return RepositoryClassWriter
     */
    public function write(Database $database, Table $table, $directory, array &$existingFiles)
    {
        $fileName = $this->generateFileName($table, $directory, '', self::FILE_NAME_SUFFIX);

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
namespace {$database->getPhpName()}\Repository;

use Janisbiz\LightOrm\MySQL\AbstractRepository;
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
