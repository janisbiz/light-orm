<?php

namespace Janisbiz\LightOrm;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Generator\Database;
use Janisbiz\LightOrm\Dms\MySQL\Generator\GeneratorFactory;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Writer\BaseModelClassWriter;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Writer\ModelClassWriter;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Writer\RepositoryClassWriter;
use Janisbiz\Heredoc\HeredocTrait;

class Generator
{
    use HeredocTrait;

    /**
     * @var GeneratorFactory
     */
    private $generatorFactory;

    /**
     * @var string
     */
    private $generatePath;

    /**
     * @param GeneratorFactory $generatorFactory
     * @param string $generatePath
     */
    public function __construct(
        GeneratorFactory $generatorFactory,
        $generatePath
    ) {
        $this->generatorFactory = $generatorFactory;
        $this->generatePath = \rtrim($generatePath, '/');
    }

    public function generate(ConnectionInterface $connection, $databaseName)
    {
        $database = $this->generatorFactory->createDatabase($databaseName, $connection);

        /**
         * @var string $modelsDirectory
         * @var string $modelsBaseDirectory
         * @var string $modelsRepositoryDirectory
         */
        \extract($this->createDirectories($database));

        /**
         * @var string[] $modelsFiles
         * @var string[] $modelsBaseFiles
         * @var string[] $modelsRepositoryFiles
         */
        \extract($this->getExistingFiles($modelsDirectory, $modelsBaseDirectory, $modelsRepositoryDirectory));

        $baseModelClassWriter = new BaseModelClassWriter();
        $modelClassWriter = new ModelClassWriter();
        $repositoryClassWriter = new RepositoryClassWriter();

        foreach ($database->getTables() as $table) {
            $baseModelClassWriter->write($database, $table, $modelsBaseDirectory, $modelsBaseFiles);
            $modelClassWriter->write($database, $table, $modelsDirectory, $modelsFiles);
            $repositoryClassWriter->write($database, $table, $modelsRepositoryDirectory, $modelsRepositoryFiles);
        }

        $this->removeUnusedFiles(
            $modelsFiles,
            $modelsBaseFiles,
            $modelsRepositoryFiles,
            $modelsDirectory,
            $modelsBaseDirectory,
            $modelsRepositoryDirectory
        );
    }

    /**
     * @param Database $database
     *
     * @return array
     */
    private function createDirectories(Database $database)
    {
        $modelsDirectory = \sprintf('%s/%s', $this->generatePath, $database->getPhpName());
        $modelsBaseDirectory = \sprintf('%s/Base/', $modelsDirectory);
        $modelsRepositoryDirectory = \sprintf('%s/Repository/', $modelsDirectory);

        if (!\file_exists($modelsBaseDirectory)) {
            \mkdir($modelsBaseDirectory, 0777, true);
        }

        if (!\file_exists($modelsRepositoryDirectory)) {
            \mkdir($modelsRepositoryDirectory, 0777, true);
        }

        return [
            'modelsDirectory' => $modelsDirectory,
            'modelsBaseDirectory' => $modelsBaseDirectory,
            'modelsRepositoryDirectory' => $modelsRepositoryDirectory,
        ];
    }

    /**
     * @param string $modelsDirectory
     * @param string $modelsBaseDirectory
     * @param string $modelsRepositoryDirectory
     *
     * @return array
     */
    private function getExistingFiles($modelsDirectory, $modelsBaseDirectory, $modelsRepositoryDirectory)
    {
        $modelsHandle = \opendir($modelsDirectory);
        $modelsFiles = [];
        while (false !== ($modelFile = \readdir($modelsHandle))) {
            if (\is_dir(\sprintf('%s/%s', $modelsDirectory, $modelFile))) {
                continue;
            }

            $modelsFiles[] = $modelFile;
        }
        \closedir($modelsHandle);
        \asort($modelsFiles);

        $modelsBaseHandle = \opendir($modelsBaseDirectory);
        $modelsBaseFiles = [];

        while (false !== ($modelBaseFile = \readdir($modelsBaseHandle))) {
            if (\is_dir(\sprintf('%s/%s', $modelsBaseDirectory, $modelBaseFile))) {
                continue;
            }

            $modelsBaseFiles[] = $modelBaseFile;
        }
        \closedir($modelsBaseHandle);
        \asort($modelsBaseFiles);

        $modelsRepositoryHandle = \opendir($modelsRepositoryDirectory);
        $modelsRepositoryFiles = [];

        while (false !== ($modelRepositoryFile = \readdir($modelsRepositoryHandle))) {
            if (\is_dir(\sprintf('%s/%s', $modelsRepositoryDirectory, $modelRepositoryFile))) {
                continue;
            }

            $modelsRepositoryFiles[] = $modelRepositoryFile;
        }
        \closedir($modelsRepositoryHandle);
        \asort($modelsRepositoryFiles);

        return [
            'modelsFiles' => $modelsFiles,
            'modelsBaseFiles' => $modelsBaseFiles,
            'modelsRepositoryFiles' => $modelsRepositoryFiles,
        ];
    }

    /**
     * @param array $modelsFiles
     * @param array $modelsBaseFiles
     * @param array $modelsRepositoryFiles
     * @param string $modelsDirectory
     * @param string $modelsBaseDirectory
     * @param string $modelsRepositoryDirectory
     *
     * @return $this
     */
    private function removeUnusedFiles(
        array &$modelsFiles,
        array &$modelsBaseFiles,
        array &$modelsRepositoryFiles,
        $modelsDirectory,
        $modelsBaseDirectory,
        $modelsRepositoryDirectory
    ) {
        foreach ($modelsFiles as $modelsFileIndex => $modelsFile) {
            $modelsFileName = \sprintf('%s/%s', $modelsDirectory, $modelsFile);

            if (!\is_dir($modelsFileName)) {
                \unlink($modelsFileName);
                unset($modelsFiles[$modelsFileIndex]);
            }
        }

        foreach ($modelsBaseFiles as $modelsBaseFileIndex => $modelsBaseFile) {
            $modelsBaseFileName = \sprintf('%s/%s', $modelsBaseDirectory, $modelsBaseFile);

            if (!\is_dir($modelsBaseFileName)) {
                \unlink($modelsBaseFileName);
                unset($modelsBaseFiles[$modelsBaseFileIndex]);
            }
        }

        foreach ($modelsRepositoryFiles as $modelsRepositoryFileIndex => $modelsRepositoryFile) {
            $modelsBaseFileName = \sprintf('%s/%s', $modelsRepositoryDirectory, $modelsRepositoryFile);

            if (!\is_dir($modelsBaseFileName)) {
                \unlink($modelsBaseFileName);
                unset($modelsRepositoryFiles[$modelsRepositoryFileIndex]);
            }
        }

        return $this;
    }
}
