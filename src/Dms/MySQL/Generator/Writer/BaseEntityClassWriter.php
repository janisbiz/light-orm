<?php

namespace Janisbiz\LightOrm\Dms\MySQL\Generator\Writer;

use Janisbiz\LightOrm\Generator\Dms\DmsDatabaseInterface;
use Janisbiz\LightOrm\Generator\Dms\DmsTableInterface;
use Janisbiz\LightOrm\Generator\Writer\AbstractWriter;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Dms\DmsColumn;
use Janisbiz\Heredoc\HeredocTrait;
use Janisbiz\LightOrm\Generator\Writer\WriterConfigInterface;
use Janisbiz\LightOrm\Generator\Writer\WriterInterface;

class BaseEntityClassWriter extends AbstractWriter
{
    use HeredocTrait;

    /**
     * @param WriterConfigInterface $writerConfig
     */
    public function __construct(WriterConfigInterface $writerConfig)
    {
        $this->writerConfig = $writerConfig;
    }

    /**
     * @param DmsDatabaseInterface $dmsDatabase
     * @param DmsTableInterface $dmsTable
     * @param array $existingFiles
     *
     * @return BaseEntityClassWriter
     */
    public function write(
        DmsDatabaseInterface $dmsDatabase,
        DmsTableInterface $dmsTable,
        array &$existingFiles
    ) {
        $fileName = $this->generateFileName($dmsDatabase, $dmsTable);

        return $this
            ->writeFile($fileName, $this->generateFileContents($dmsDatabase, $dmsTable))
            ->removeFileFromExistingFiles($fileName, $existingFiles)
        ;
    }

    /**
     * @return WriterConfigInterface
     */
    protected function getWriterConfig()
    {
        return $this->writerConfig;
    }

    /**
     * @param DmsDatabaseInterface $dmsDatabase
     * @param DmsTableInterface $dmsTable
     *
     * @return string
     */
    protected function generateFileContents(DmsDatabaseInterface $dmsDatabase, DmsTableInterface $dmsTable)
    {
        $gettersAndSetters = $dmsTable->getDmsColumns();
        $gettersAndSetters = \implode("\n\n", \array_map(
            function (DmsColumn $column) use ($dmsTable) {
                /** phpcs:disable */
                return /** @lang PHP */
                    <<<PHP

    /**
     * @return {$this->heredoc($column->isNullable() || 'auto_increment' === $column->getExtra() ? 'null|' : '')}{$column->getPhpType()}
     */
    public function get{$column->getPhpName()}()
    {
        return \$this->data['{$column->getName()}'];
    }
    
    /**
     * @param {$this->heredoc($column->isNullable() || 'auto_increment' === $column->getExtra() ? 'null|' : '')}{$column->getPhpType()} \${$this->heredoc(\lcfirst($column->getPhpName()))}
     *
     * @return \$this
     */
    public function set{$column->getPhpName()}(\${$this->heredoc(\lcfirst($column->getPhpName()))})
    {
        \$this->data['{$column->getName()}'] = \${$this->heredoc(\lcfirst($column->getPhpName()))};

        return \$this;
    }
PHP;
                /** phpcs:enable */
            },
            $gettersAndSetters
        ));

        $columnsConstants = $dmsTable->getDmsColumns();
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

        $primaryKeys = $dmsTable->getDmsColumns();
        $primaryKeys = \implode(
            "\n            ",
            \array_filter(\array_map(
                function (DmsColumn $column) {
                    if ($column->getKey() !== 'PRI') {
                        return null;
                    }

                    return \sprintf('static::COLUMN_%s,', \mb_strtoupper($column->getName()));
                },
                $primaryKeys
            ))
        );

        $primaryKeysAutoIncrement = $dmsTable->getDmsColumns();
        $primaryKeysAutoIncrement = \implode(
            "\n            ",
            \array_filter(\array_map(
                function (DmsColumn $column) {
                    if ($column->getExtra() !== 'auto_increment') {
                        return null;
                    }

                    return \sprintf('static::COLUMN_%s,', \mb_strtoupper($column->getName()));
                },
                $primaryKeysAutoIncrement
            ))
        );

        $columns = $dmsTable->getDmsColumns();
        $columns = \implode(
            "\n            ",
            \array_map(
                function (DmsColumn $column) {
                    return \sprintf('static::COLUMN_%s,', \mb_strtoupper($column->getName()));
                },
                $columns
            )
        );

        /** phpcs:disable */
        return /** @lang PHP */
            <<<PHP
<?php

namespace {$this->generateNamespace($dmsDatabase)};

use Janisbiz\LightOrm\Entity\BaseEntity;

class {$this->generateClassName($dmsTable)} extends BaseEntity
{
    const {$this->heredoc(WriterInterface::CLASS_CONSTANT_DATABASE_NAME)} = '{$dmsDatabase->getName()}';
    const {$this->heredoc(WriterInterface::CLASS_CONSTANT_TABLE_NAME)} = '{$dmsDatabase->getName()}.{$dmsTable->getName()}';
    
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
    {$gettersAndSetters}
}

PHP;
        /** phpcs:enable */
    }
}
