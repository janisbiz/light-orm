<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\Generator\Writer;

use Janisbiz\LightOrm\Generator\Dms\DmsDatabaseInterface;
use Janisbiz\LightOrm\Generator\Dms\DmsTableInterface;
use Janisbiz\LightOrm\Generator\Writer\AbstractWriter;
use Janisbiz\LightOrm\Generator\Writer\WriterConfigInterface;

class EntityClassWriter extends AbstractWriter
{
    public function __construct(
        WriterConfigInterface $writerConfig,
        protected BaseEntityClassWriter $baseEntityClassWriter
    ) {
        parent::__construct($writerConfig);
    }

    public function write(
        DmsDatabaseInterface $dmsDatabase,
        DmsTableInterface $dmsTable,
        array &$existingFiles
    ): static {
        $fileName = $this->generateFileName($dmsDatabase, $dmsTable);

        return $this
            ->writeFile($fileName, $this->generateFileContents($dmsDatabase, $dmsTable), true)
            ->removeFileFromExistingFiles($fileName, $existingFiles)
        ;
    }

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
