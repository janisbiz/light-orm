<?php

namespace Janisbiz\LightOrm\Generator\Writer;

use Janisbiz\LightOrm\Generator\Dms\DmsDatabaseInterface;
use Janisbiz\LightOrm\Generator\Dms\DmsTableInterface;

abstract class AbstractWriter implements WriterInterface
{
    /**
     * @var WriterConfigInterface
     */
    protected $writerConfig;

    /**
     * @param DmsDatabaseInterface $dmsDatabase
     *
     * @return string
     */
    public function generateNamespace(DmsDatabaseInterface $dmsDatabase)
    {
        return \rtrim(
            \sprintf(
                '%s\\%s\\%s%s',
                $this->getWriterConfig()->getNamespace(),
                $dmsDatabase->getPhpName(),
                $this->getWriterConfig()->getClassPrefix(),
                $this->getWriterConfig()->getClassSuffix()
            ),
            '\\'
        );
    }

    /**
     * @param DmsTableInterface $dmsTable
     *
     * @return string
     */
    public function generateClassName(DmsTableInterface $dmsTable)
    {
        return $this->getWriterConfig()->getClassPrefix()
            . $dmsTable->getPhpName()
            . $this->getWriterConfig()->getClassSuffix();
    }

    /**
     * @param DmsDatabaseInterface $dmsDatabase
     * @param DmsTableInterface $dmsTable
     *
     * @return string
     */
    public function generateFQDN(DmsDatabaseInterface $dmsDatabase, DmsTableInterface $dmsTable)
    {
        return \sprintf('%s\\%s', $this->generateNamespace($dmsDatabase), $this->generateClassName($dmsTable));
    }

    /**
     * @param DmsDatabaseInterface $dmsDatabase
     *
     * @return array|string[]
     */
    public function read(DmsDatabaseInterface $dmsDatabase)
    {
        $fileDirectory = $this->generateFileDirectory($dmsDatabase);
        $handle = \opendir($fileDirectory);
        $files = [];
        while (false !== ($file = \readdir($handle))) {
            $fileName = \implode(
                '',
                [
                    $fileDirectory,
                    DIRECTORY_SEPARATOR,
                    $file,
                ]
            );

            if (\is_dir($fileName)) {
                continue;
            }

            $files[$fileName] = $file;
        }
        \closedir($handle);
        \asort($files);

        return $files;
    }

    /**
     * @return WriterConfigInterface
     */
    abstract protected function getWriterConfig();

    /**
     * @param DmsDatabaseInterface $dmsDatabase
     *
     * @return string
     */
    protected function generateFileDirectory(DmsDatabaseInterface $dmsDatabase)
    {
        return \rtrim(
            \implode(
                '',
                [
                    $this->getWriterConfig()->getDirectory(),
                    DIRECTORY_SEPARATOR,
                    $dmsDatabase->getPhpName(),
                    DIRECTORY_SEPARATOR,
                    $this->getWriterConfig()->getClassPrefix(),
                    $this->getWriterConfig()->getClassSuffix()
                ]
            ),
            DIRECTORY_SEPARATOR
        );
    }

    /**
     * @param DmsDatabaseInterface $dmsDatabase
     * @param DmsTableInterface $dmsTable
     *
     * @return string
     */
    protected function generateFileName(DmsDatabaseInterface $dmsDatabase, DmsTableInterface $dmsTable)
    {
        return \sprintf(
            '%s.php',
            \implode(
                '',
                [
                    $this->generateFileDirectory($dmsDatabase),
                    DIRECTORY_SEPARATOR,
                    $this->getWriterConfig()->getClassPrefix(),
                    $dmsTable->getPhpName(),
                    $this->getWriterConfig()->getClassSuffix(),
                ]
            )
        );
    }

    /**
     * @param string $fileName
     * @param string $fileContent
     * @param bool $skipIfExists
     *
     * @return $this
     */
    protected function writeFile($fileName, $fileContent, $skipIfExists = false)
    {
        if (true === $skipIfExists && \file_exists($fileName)) {
            return $this;
        }

        $fileDirectory = \dirname($fileName);
        if (!\file_exists($fileDirectory)) {
            \mkdir($fileDirectory, 0777, true);
        }

        $file = \fopen($fileName, 'w');
        \fwrite($file, $fileContent);
        \fclose($file);

        return $this;
    }

    /**
     * @param string $fileName
     * @param array $existingFiles
     *
     * @return $this
     */
    protected function removeFileFromExistingFiles($fileName, array &$existingFiles)
    {
        if (false !== ($fileIndex = \array_search(\basename($fileName), $existingFiles))) {
            unset($existingFiles[$fileIndex]);
        }

        return $this;
    }
}
