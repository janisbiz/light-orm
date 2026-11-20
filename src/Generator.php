<?php declare(strict_types=1);

namespace Janisbiz\LightOrm;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Dms\MySQL\Generator\DmsFactory;
use Janisbiz\Heredoc\HeredocTrait;
use Janisbiz\LightOrm\Generator\Writer\WriterInterface;

class Generator
{
    use HeredocTrait;

    /**
     * @var WriterInterface[]
     */
    protected array $writers = [];

    public function __construct(protected DmsFactory $dmsFactory)
    {
    }

    public function addWriter(WriterInterface $writer): static
    {
        $writerClass = $writer::class;

        if (!array_key_exists($writerClass, $this->writers)) {
            $this->writers[$writerClass] = $writer;
        }

        return $this;
    }

    public function generate(ConnectionInterface $connection, string $databaseName): static
    {
        $dmsDatabase = $this->dmsFactory->createDmsDatabase($databaseName, $connection);

        foreach ($this->writers as $writer) {
            $existingFiles = $writer->read($dmsDatabase);

            foreach ($dmsDatabase->getDmsTables() as $dmsTable) {
                $writer->write($dmsDatabase, $dmsTable, $existingFiles);
            }

            $this->removeExistingFiles($existingFiles);
        }

        return $this;
    }

    protected function removeExistingFiles(array &$existingFiles): static
    {
        foreach (array_keys($existingFiles) as $path) {
            if (!is_dir($path) && file_exists($path)) {
                unlink($path);
                unset($existingFiles[$path]);
            }
        }

        return $this;
    }
}
