<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Behat\Bootstrap;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Janisbiz\LightOrm\ConnectionPool;
use Symfony\Component\Yaml\Parser;

abstract class AbstractFeatureContext implements Context
{
    const CONFIG_FILE_NAME = 'light-orm.yaml';

    /**
     * @var array
     */
    private $config;

    /**
     * @var ConnectionPool
     */
    protected $connectionPool;

    /**
     * @var string
     */
    protected $rootDir;

    public function __construct()
    {
        $this->connectionPool = new ConnectionPool();
        $this->rootDir = \implode(
            '',
            [
                __DIR__,
                DIRECTORY_SEPARATOR,
                '..',
                DIRECTORY_SEPARATOR,
                '..',
                DIRECTORY_SEPARATOR,
                '..',
            ]
        );
    }

    /**
     * @param null|string $connectionName
     *
     * @return array
     * @throws \Exception
     */
    protected function getConnectionConfig(?string $connectionName = null): array
    {
        if (null !== $connectionName) {
            if (!isset($this->getConfig()['connections'][$connectionName])) {
                throw new \Exception(\sprintf('Could not get connection "%s"', $connectionName));
            }

            return $this->getConfig()['connections'][$connectionName];
        }

        return $this->getConfig()['connections'];
    }

    /**
     * @param string $connectionName
     *
     * @return array
     * @throws \Exception
     */
    protected function getWritersConfig(string $connectionName): array
    {
        if (!isset($this->getConfig()['generator'][$connectionName]['writers'])) {
            throw new \Exception(\sprintf('Could not get writer config for connection "%s"', $connectionName));
        }

        return $this->getConfig()['generator'][$connectionName]['writers'];
    }

    /**
     * @param TableNode $tableNode
     *
     * @return TableNode
     */
    protected function normalizeTableNode(TableNode $tableNode): TableNode
    {
        $tableNodeParsedArray = [
            0 => \array_map(
                function (string $column): string {
                    return \explode(':', $column)[0];
                },
                $tableNode->getRow(0)
            ),
        ];

        foreach ($tableNode as $rowIndex => $nodeRow) {
            $nodeRowParsed = [];
            foreach ($nodeRow as $cellNameWithDataType => $cellValue) {
                $cellNameWithDataType = \explode(':', $cellNameWithDataType);
                $cellDataType = $cellNameWithDataType[1] ?? false;

                if (false !== $cellDataType) {
                    if (0 === \strpos($cellDataType, '?')) {
                        if ('' === $cellValue) {
                            $cellValue = null;
                        } else {
                            $cellDataType = \ltrim($cellDataType, '?');
                            \settype($cellValue, $cellDataType);
                        }
                    } else {
                        \settype($cellValue, $cellDataType);
                    }
                }

                $nodeRowParsed[] = $cellValue;
            }

            $tableNodeParsedArray[$rowIndex + 1] = $nodeRowParsed;
        }

        /**
         * Handling node validation. This is known exception, as it checks for type of string in cell values. If it is
         * not string, exception is thrown, but rest validation is valid.
         */
        try {
            new TableNode($tableNodeParsedArray);
        } catch (\Throwable $e) {
            if (70 !== $e->getLine() || 'Table is not two-dimensional.' !== $e->getMessage()) {
                throw $e;
            }
        }

        $tableNodeParsed = new TableNode([]);

        $tableProperty = new \ReflectionProperty($tableNodeParsed, 'table');
        $tableProperty->setAccessible(true);
        $tableProperty->setValue($tableNodeParsed, $tableNodeParsedArray);

        return $tableNodeParsed;
    }

    /**
     * @return array
     */
    private function getConfig(): array
    {
        if (null === $this->config) {
            $this->config = (new Parser())
                ->parseFile(\implode(
                    '',
                    [
                        JANISBIZ_LIGHT_ORM_BEHAT_CONFIG_DIR,
                        static::CONFIG_FILE_NAME,
                    ]
                ))['light-orm']
            ;

            foreach ($this->config['generator'] as &$connectionConfigs) {
                foreach ($connectionConfigs['writers'] as &$writerConfig) {
                    $writerConfig['directory'] = \preg_replace(
                        '/\/\\\/',
                        DIRECTORY_SEPARATOR,
                        $writerConfig['directory']
                    );
                }
            }
        }

        return $this->config;
    }
}
