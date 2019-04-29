<?php

namespace Janisbiz\LightOrm\Dms\MySQL\Generator\Writer;

use Janisbiz\LightOrm\Generator\Writer\AbstractWriter;
use Janisbiz\LightOrm\Generator\Database;
use Janisbiz\LightOrm\Generator\Table;
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
        return /** @lang PHP */
            <<<PHP
<?php
namespace {$database->getPhpName()}\Repository;

use Janisbiz\LightOrm\Repository\AbstractRepository;
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
