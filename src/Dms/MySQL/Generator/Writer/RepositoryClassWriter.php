<?php

namespace Janisbiz\LightOrm\Dms\MySQL\Generator\Writer;

use Janisbiz\LightOrm\Generator\Dms\DmsDatabaseInterface;
use Janisbiz\LightOrm\Generator\Dms\DmsTableInterface;
use Janisbiz\LightOrm\Generator\Writer\AbstractWriter;
use Janisbiz\Heredoc\HeredocTrait;
use Janisbiz\LightOrm\Generator\Writer\WriterConfigInterface;

class RepositoryClassWriter extends AbstractWriter
{
    use HeredocTrait;

    /**
     * @var EntityClassWriter
     */
    private $entityClassWriter;

    /**
     * @param WriterConfigInterface $writerConfig
     * @param EntityClassWriter $entityClassWriter
     */
    public function __construct(WriterConfigInterface $writerConfig, EntityClassWriter $entityClassWriter)
    {
        $this->writerConfig = $writerConfig;
        $this->entityClassWriter = $entityClassWriter;
    }

    /**
     * @param DmsDatabaseInterface $dmsDatabase
     * @param DmsTableInterface $dmsTable
     * @param array $existingFiles
     *
     * @return RepositoryClassWriter
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
        return /** @lang PHP */
            <<<PHP
<?php

namespace {$this->generateNamespace($dmsDatabase)};

use Janisbiz\LightOrm\Dms\MySQL\Repository\AbstractRepository;
use {$this->entityClassWriter->generateFQDN($dmsDatabase, $dmsTable)};

class {$this->generateClassName($dmsTable)} extends AbstractRepository
{
    /**
    * @return string
    */
    protected function getModelClass()
    {
        return {$this->entityClassWriter->generateClassName($dmsTable)}::class;
    }
}

PHP;
    }
}
