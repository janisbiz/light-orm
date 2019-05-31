<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\Generator\Writer;

use Janisbiz\LightOrm\Generator\Dms\DmsDatabaseInterface;
use Janisbiz\LightOrm\Generator\Dms\DmsTableInterface;
use Janisbiz\LightOrm\Generator\Writer\AbstractWriter;
use Janisbiz\LightOrm\Generator\Writer\WriterConfigInterface;

class EntityClassWriter extends AbstractWriter
{
    /**
     * @var BaseEntityClassWriter
     */
    protected $baseEntityClassWriter;

    /**
     * @param WriterConfigInterface $writerConfig
     * @param BaseEntityClassWriter $baseEntityClassWriter
     */
    public function __construct(WriterConfigInterface $writerConfig, BaseEntityClassWriter $baseEntityClassWriter)
    {
        $this->writerConfig = $writerConfig;
        $this->baseEntityClassWriter = $baseEntityClassWriter;
    }

    /**
     * @param DmsDatabaseInterface $dmsDatabase
     * @param DmsTableInterface $dmsTable
     * @param array $existingFiles
     *
     * @return EntityClassWriter
     */
    public function write(
        DmsDatabaseInterface $dmsDatabase,
        DmsTableInterface $dmsTable,
        array &$existingFiles
    ) {
        $fileName = $this->generateFileName($dmsDatabase, $dmsTable);

        return $this
            ->writeFile($fileName, $this->generateFileContents($dmsDatabase, $dmsTable), true)
            ->removeFileFromExistingFiles($fileName, $existingFiles)
        ;
    }

    /**
     * @return WriterConfigInterface
     */
    protected function getWriterConfig(): WriterConfigInterface
    {
        return $this->writerConfig;
    }

    /**
     * @param DmsDatabaseInterface $dmsDatabase
     * @param DmsTableInterface $dmsTable
     *
     * @return string
     */
    protected function generateFileContents(DmsDatabaseInterface $dmsDatabase, DmsTableInterface $dmsTable): string
    {
        return /** @lang PHP */<<<PHP
<?php declare(strict_types=1);

namespace {$this->generateNamespace($dmsDatabase)};

use {$this->baseEntityClassWriter->generateFQDN($dmsDatabase, $dmsTable)};

class {$this->generateClassName($dmsTable)} extends {$this->baseEntityClassWriter->generateClassName($dmsTable)}
{
}

PHP;
    }
}
