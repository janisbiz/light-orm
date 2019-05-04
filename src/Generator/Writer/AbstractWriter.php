<?php

namespace Janisbiz\LightOrm\Generator\Writer;

use Janisbiz\LightOrm\Generator\Dms\DmsTable;

abstract class AbstractWriter implements WriterInterface
{
    /**
     * @param DmsTable $table
     * @param string $directory
     * @param string $prefix
     * @param string $suffix
     *
     * @return string
     */
    protected function generateFileName(DmsTable $table, $directory, $prefix = '', $suffix = '')
    {
        return \sprintf('%s/%s%s%s.php', $directory, $prefix, $table->getPhpName(), $suffix);
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
