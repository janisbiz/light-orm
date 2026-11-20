<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Generator\Writer;

use Janisbiz\LightOrm\Generator\Dms\DmsDatabaseInterface;
use Janisbiz\LightOrm\Generator\Dms\DmsTableInterface;

abstract class AbstractWriter implements WriterInterface
{
    public function __construct(protected WriterConfigInterface $writerConfig)
    {
        $this->writerConfig = $writerConfig;
    }

    public function generateNamespace(DmsDatabaseInterface $dmsDatabase): string
    {
        return trim(
            sprintf(
                '%s\\%s\\%s%s',
                $this->getWriterConfig()->getNamespace(),
                $dmsDatabase->getPhpName(),
                $this->getWriterConfig()->getClassPrefix(),
                $this->getWriterConfig()->getClassSuffix()
            ),
            '\\'
        );
    }

    public function generateClassName(DmsTableInterface $dmsTable): string
    {
        return implode(
            '',
            [
                $this->getWriterConfig()->getClassPrefix(),
                $dmsTable->getPhpName(),
                $this->getWriterConfig()->getClassSuffix()
            ]
        );
    }

    public function generateFQDN(DmsDatabaseInterface $dmsDatabase, DmsTableInterface $dmsTable): string
    {
        return sprintf('%s\\%s', $this->generateNamespace($dmsDatabase), $this->generateClassName($dmsTable));
    }

    /**
     * @return string[]
     */
    public function read(DmsDatabaseInterface $dmsDatabase): array
    {
        $fileDirectory = $this->generateFileDirectory($dmsDatabase);
        $handle = @opendir($fileDirectory);

        $files = [];

        if (false === $handle) {
            return $files;
        }

        while (false !== ($file = readdir($handle))) {
            $fileName = implode(
                '',
                [
                    $fileDirectory,
                    DIRECTORY_SEPARATOR,
                    $file,
                ]
            );

            if (is_dir($fileName)) {
                continue;
            }

            $files[$fileName] = $file;
        }
        closedir($handle);
        asort($files);

        return $files;
    }

    protected function getWriterConfig(): WriterConfigInterface
    {
        return $this->writerConfig;
    }

    protected function generateFileDirectory(DmsDatabaseInterface $dmsDatabase): string
    {
        return rtrim(
            implode(
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

    protected function generateFileName(DmsDatabaseInterface $dmsDatabase, DmsTableInterface $dmsTable): string
    {
        return sprintf(
            '%s.php',
            implode(
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

    protected function writeFile(string $fileName, string $fileContent, bool $skipIfExists = false): static
    {
        if (true === $skipIfExists && file_exists($fileName)) {
            return $this;
        }

        $fileDirectory = dirname($fileName);
        if (!file_exists($fileDirectory)) {
            mkdir($fileDirectory, 0777, true);
        }

        $file = fopen($fileName, 'w');
        fwrite($file, $fileContent);
        fclose($file);

        return $this;
    }

    protected function removeFileFromExistingFiles(string $fileName, array &$existingFiles): static
    {
        foreach ($existingFiles as $i => $existingFile) {
            if (basename($fileName) === basename($existingFile)) {
                unset($existingFiles[$i]);
            }
        }

        return $this;
    }
}
