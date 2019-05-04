<?php

namespace Janisbiz\LightOrm\Dms\MySQL\Generator\Writer;

use Janisbiz\LightOrm\Generator\Writer\AbstractWriter;
use Janisbiz\LightOrm\Generator\Dms\DmsColumn;
use Janisbiz\LightOrm\Generator\Dms\DmsDatabase;
use Janisbiz\LightOrm\Generator\Dms\DmsTable;
use Janisbiz\Heredoc\HeredocTrait;

class BaseEntityClassWriter extends AbstractWriter
{
    use HeredocTrait;

    const FILE_NAME_PREFIX = 'Base';

    const CLASS_CONSTANT_DATABASE_NAME = 'DATABASE_NAME';
    const CLASS_CONSTANT_TABLE_NAME = 'TABLE_NAME';

    /**
     * @param DmsDatabase $database
     * @param DmsTable $table
     * @param string $directory
     * @param array $existingFiles
     *
     * @return BaseEntityClassWriter
     */
    public function write(DmsDatabase $database, DmsTable $table, $directory, array &$existingFiles)
    {
        $fileName = $this->generateFileName($table, $directory, self::FILE_NAME_PREFIX);

        return $this
            ->writeFile($fileName, $this->generateFileContents($database, $table))
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
        $phpDoc = $table->getColumns();
        $phpDoc = \implode("\n *\n", \array_map(function (DmsColumn $column) {
            return \sprintf(
                " * @method %s get%s(bool \$escapeHtml = false)\n * @method \$this set%s(%s \$val)",
                \implode('|', \array_unique([$column->getPhpDefaultType(), $column->getPhpType()])),
                $column->getPhpName(),
                $column->getPhpName(),
                \implode('|', \array_unique([$column->getPhpDefaultType(), $column->getPhpType()]))
            );
        }, $phpDoc));

        $columnsConstants = $table->getColumns();
        $columnsConstants = \implode(
            "\n    ",
            \array_map(
                function (DmsColumn $column) {
                    return \sprintf(
                        'const COLUMN_%s = \'%s\';',
                        \mb_strtoupper($column->getName()),
                        $column->getName()
                    );
                },
                $columnsConstants
            )
        );

        $primaryKeys = $table->getColumns();
        $primaryKeys = \implode(
            "\n            ",
            \array_filter(\array_map(
                function (DmsColumn $column) {
                    if ($column->getKey() !== 'PRI') {
                        return null;
                    }

                    return \sprintf('self::COLUMN_%s,', \mb_strtoupper($column->getName()));
                },
                $primaryKeys
            ))
        );

        $primaryKeysAutoIncrement = $table->getColumns();
        $primaryKeysAutoIncrement = \implode(
            "\n            ",
            \array_filter(\array_map(
                function (DmsColumn $column) {
                    if ($column->getExtra() !== 'auto_increment') {
                        return null;
                    }

                    return \sprintf('self::COLUMN_%s,', \mb_strtoupper($column->getName()));
                },
                $primaryKeysAutoIncrement
            ))
        );

        $columns = $table->getColumns();
        $columns = \implode(
            "\n            ",
            \array_map(
                function (DmsColumn $column) {
                    return \sprintf('self::COLUMN_%s,', \mb_strtoupper($column->getName()));
                },
                $columns
            )
        );

        return /** @lang PHP */
            <<<PHP
<?php
namespace {$database->getPhpName()}\Base;

use Janisbiz\LightOrm\Entity\BaseEntity;

/**
{$phpDoc}
**/
class {$this->heredoc(self::FILE_NAME_PREFIX)}{$table->getPhpName()} extends BaseEntity
{
    const {$this->heredoc(self::CLASS_CONSTANT_DATABASE_NAME)} = '{$database->getName()}';
    const {$this->heredoc(self::CLASS_CONSTANT_TABLE_NAME)} = '{$database->getName()}.{$table->getName()}';
    
    {$columnsConstants}
    
    /**
     * @param bool \$isNew
     */
    public function __construct(\$isNew = true)
    {
        \$this->primaryKeys = [
            {$primaryKeys}
        ];
        \$this->primaryKeysAutoIncrement = [
            {$primaryKeysAutoIncrement}
        ];
        \$this->columns = [
            {$columns}
        ];
        
        \$this->isNew = \$isNew;
        if (empty(\$this->data)) {
            \$this->isNew = true;
        }
    }
}

PHP;
    }
}
