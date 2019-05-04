<?php

namespace Janisbiz\LightOrm;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Dms\MySQL\Generator\DmsFactory;
use Janisbiz\Heredoc\HeredocTrait;
use Janisbiz\LightOrm\Generator\Writer\WriterInterface;

class Generator
{
    use HeredocTrait;

    /**
     * @var DmsFactory
     */
    protected $dmsFactory;

    /**
     * @var WriterInterface[]
     */
    protected $writers = [];

    /**
     * @param DmsFactory $dmsFactory
     */
    public function __construct(DmsFactory $dmsFactory)
    {
        $this->dmsFactory = $dmsFactory;
    }

    /**
     * @param WriterInterface $writer
     *
     * @return $this
     */
    public function addWriter(WriterInterface $writer)
    {
        $writerClass = \get_class($writer);

        if (!\array_key_exists($writerClass, $this->writers)) {
            $this->writers[$writerClass] = $writer;
        }

        return $this;
    }

    /**
     * @param ConnectionInterface $connection
     * @param string $databaseName
     *
     * @return $this
     */
    public function generate(ConnectionInterface $connection, $databaseName)
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

    /**
     * @param string[] $existingFiles
     *
     * @return $this
     */
    protected function removeExistingFiles(array &$existingFiles) {
        foreach (\array_keys($existingFiles) as $path) {
            if (!\is_dir($path)) {
                \unlink($path);
                unset($existingFiles[$path]);
            }
        }

        return $this;
    }
}
