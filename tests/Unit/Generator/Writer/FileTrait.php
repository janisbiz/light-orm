<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Generator\Writer;

trait FileTrait
{
    /**
     * @param string $directory
     */
    protected function removeDirectoryRecursive($directory)
    {
        if (\is_dir($directory)) {
            $recursiveDirectoryIterator = new \RecursiveDirectoryIterator(
                $directory,
                \RecursiveDirectoryIterator::SKIP_DOTS
            );

            $files = new \RecursiveIteratorIterator(
                $recursiveDirectoryIterator,
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $file) {
                if ($file->isDir()) {
                    \rmdir($file->getRealPath());
                } else {
                    \unlink($file->getRealPath());
                }
            }

            \rmdir($directory);
        }
    }

    /**
     * @param array $fileNames
     * @param string $fileDirectory
     *
     * @return array
     */
    protected function createTestFiles(array $fileNames, $fileDirectory)
    {
        if (!\file_exists($fileDirectory)) {
            \mkdir($fileDirectory, 0777, true);
        }

        $files = [];
        foreach ($fileNames as $fileName) {
            $files[] = sprintf('%s/%s', \rtrim($fileDirectory, '/'), $fileName);
        }

        foreach ($files as $file) {
            \file_put_contents($file, '');
        }

        \sort($files);

        return $files;
    }
}
