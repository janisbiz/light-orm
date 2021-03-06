<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Behat\Features\Dms\MySQL\Generator;

use Behat\Gherkin\Node\TableNode;
use Janisbiz\LightOrm\Dms\MySQL\Connection\ConnectionConfig as MySQLConnectionConfig;
use Janisbiz\LightOrm\Dms\MySQL\Generator\DmsFactory as MySQLDmsFactory;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Writer\BaseEntityClassWriter;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Writer\EntityClassWriter;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Writer\RepositoryClassWriter;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Writer\WriterConfig as MySQLWriterConfig;
use Janisbiz\LightOrm\Generator;
use Janisbiz\LightOrm\Generator\Writer\WriterInterface;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\AbstractFeatureContext;

class GeneratorFeatureContext extends AbstractFeatureContext
{
    /**
     * @var string
     */
    private $connectionName;

    /**
     * @var Generator
     */
    private $generator;

    /**
     * @var WriterInterface[]
     */
    private $writers = [];

    /**
     * @Given /^I create generator for connection "(\w+)"$/
     *
     * @param string $connectionName
     *
     * @throws \Exception
     */
    public function iCreateGeneratorForConnection(string $connectionName)
    {
        $this->connectionName = $connectionName;

        switch ($this->getConnectionConfig($this->connectionName)['adapter']) {
            case MySQLConnectionConfig::ADAPTER:
                $this->generator = new Generator(new MySQLDmsFactory());

                return;
        }

        throw new \Exception(\sprintf('Could not create generator for connection "%s"!', $this->connectionName));
    }

    /**
     * @Given I add writers to generator
     *
     * @throws \Exception
     */
    public function iAddWritersToGenerator()
    {
        $this->addWritersToGenerator();
    }

    /**
     * @Given /^I add writers to generator with directory override "(.*)"$/
     *
     * @param null|string $directoryOverride
     */
    public function iAddWritersToGeneratorWithDirectoryOverride(string $directoryOverride)
    {
        $directoryOverride = \preg_replace('/\/\\\/', DIRECTORY_SEPARATOR, $directoryOverride);

        $this->addWritersToGenerator($directoryOverride);
    }

    /**
     * @When I run generator
     */
    public function iRunGenerator()
    {
        $this->generator->generate($this->connectionPool->getConnection($this->connectionName), $this->connectionName);
    }

    /**
     * @Then /^Then I have following files generated:$/
     *
     * @param TableNode $files
     *
     * @throws \Exception
     */
    public function iShouldGetTheseRowsInDatabaseFilteredByScopeId(TableNode $files)
    {
        foreach ($this->normalizeTableNode($files) as $file) {
            $relativeFilePath = $file['path'];
            $absoluteFilePath = \implode(
                '',
                [
                    $this->rootDir,
                    DIRECTORY_SEPARATOR,
                    $relativeFilePath
                ]
            );

            if (!\file_exists($absoluteFilePath)) {
                throw new \Exception(\sprintf('File "%s" does not exist!', $relativeFilePath));
            }
        }
    }

    /**
     * @Given /^I remove directory "(.*)"$/
     *
     * @param string $directory
     */
    public function iRemoveDirectory(string $directory)
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
     * @param null|string $directoryOverride
     */
    private function addWritersToGenerator(string $directoryOverride = null)
    {
        switch ($this->getConnectionConfig($this->connectionName)['adapter']) {
            case MySQLConnectionConfig::ADAPTER:
                $this->addMySQLWriters($directoryOverride);

                return;
        }

        throw new \Exception(\sprintf('Could not add writers for connection "%s"!', $this->connectionName));
    }

    /**
     * @param null|string $directoryOverride
     *
     * @throws \Exception
     */
    private function addMySQLWriters(?string $directoryOverride)
    {
        foreach ($this->getWritersConfig($this->connectionName) as $writerClass => $writerConfig) {
            switch ($writerClass) {
                case BaseEntityClassWriter::class:
                    $this->writers[BaseEntityClassWriter::class] = new BaseEntityClassWriter(
                        new MySQLWriterConfig(
                            $directoryOverride ?: $writerConfig['directory'],
                            $writerConfig['namespace'],
                            !empty($writerConfig['classPrefix']) ? $writerConfig['classPrefix'] : '',
                            !empty($writerConfig['classSuffix']) ? $writerConfig['classSuffix'] : ''
                        )
                    );

                    break;

                case EntityClassWriter::class:
                    $this->writers[EntityClassWriter::class] = new EntityClassWriter(
                        new MySQLWriterConfig(
                            $directoryOverride ?: $writerConfig['directory'],
                            $writerConfig['namespace'],
                            !empty($writerConfig['classPrefix']) ? $writerConfig['classPrefix'] : '',
                            !empty($writerConfig['classSuffix']) ? $writerConfig['classSuffix'] : ''
                        ),
                        $this->writers[BaseEntityClassWriter::class]
                    );

                    break;

                case RepositoryClassWriter::class:
                    $this->writers[RepositoryClassWriter::class] = new RepositoryClassWriter(
                        new MySQLWriterConfig(
                            $directoryOverride ?: $writerConfig['directory'],
                            $writerConfig['namespace'],
                            !empty($writerConfig['classPrefix']) ? $writerConfig['classPrefix'] : '',
                            !empty($writerConfig['classSuffix']) ? $writerConfig['classSuffix'] : ''
                        ),
                        $this->writers[EntityClassWriter::class]
                    );

                    break;
            }

            $this->generator->addWriter($this->writers[$writerClass]);
        }
    }
}
