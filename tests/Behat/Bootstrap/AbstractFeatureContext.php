<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Behat\Bootstrap;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Exception;
use Janisbiz\LightOrm\ConnectionPool;
use ReflectionProperty;
use Symfony\Component\Yaml\Parser;

abstract class AbstractFeatureContext implements Context
{
    const CONFIG_FILE_NAME = 'light-orm.yaml';

    private ?array $config = null;

    protected ConnectionPool $connectionPool;

    protected string $rootDir;

    public function __construct()
    {
        $this->connectionPool = new ConnectionPool();
        $this->rootDir = implode(
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
     * @throws Exception
     */
    protected function getConnectionConfig(?string $connectionName = null): array
    {
        if (null !== $connectionName) {
            if (!isset($this->getConfig()['connections'][$connectionName])) {
                throw new Exception(sprintf('Could not get connection "%s"', $connectionName));
            }

            return $this->getConfig()['connections'][$connectionName];
        }

        return $this->getConfig()['connections'];
    }

    /**
     * @throws Exception
     */
    protected function getWritersConfig(string $connectionName): array
    {
        if (!isset($this->getConfig()['generator'][$connectionName]['writers'])) {
            throw new Exception(sprintf('Could not get writer config for connection "%s"', $connectionName));
        }

        return $this->getConfig()['generator'][$connectionName]['writers'];
    }

    protected function normalizeTableNode(TableNode $tableNode): TableNode
    {
        $tableNodeParsedArray = [
            0 => array_map(
                fn(string $column): string => explode(':', $column)[0],
                $tableNode->getRow(0)
            ),
        ];

        foreach ($tableNode as $rowIndex => $nodeRow) {
            $nodeRowParsed = [];
            foreach ($nodeRow as $cellNameWithDataType => $cellValue) {
                $cellNameWithDataType = explode(':', $cellNameWithDataType);
                $cellDataType = $cellNameWithDataType[1] ?? false;

                if (false !== $cellDataType) {
                    if (str_starts_with($cellDataType, '?')) {
                        if ('' === $cellValue) {
                            $cellValue = null;
                        } else {
                            $cellDataType = ltrim($cellDataType, '?');
                            settype($cellValue, $cellDataType);
                        }
                    } else {
                        settype($cellValue, $cellDataType);
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
        // try {
        //     new TableNode($tableNodeParsedArray);
        // } catch (\Throwable $e) {
        // fwrite(STDOUT, var_export($e->getMessage(), true));
        //     if (70 !== $e->getLine() || 'Table is not two-dimensional.' !== $e->getMessage()) {
        //         throw $e;
        //     }
        // }


        // fwrite(STDOUT, var_export($tableNodeParsedArray, true));

        $tableNodeParsed = new TableNode([]);

        $tableProperty = new ReflectionProperty($tableNodeParsed, 'table');
        $tableProperty->setValue($tableNodeParsed, $tableNodeParsedArray);

        return $tableNodeParsed;
    }

    private function getConfig(): array
    {
        if (null === $this->config) {
            $this->config = (new Parser())
                ->parseFile(implode(
                    '',
                    [
                        JANISBIZ_LIGHT_ORM_BEHAT_CONFIG_DIR,
                        static::CONFIG_FILE_NAME,
                    ]
                ))['light-orm']
            ;

            foreach ($this->config['generator'] as &$connectionConfigs) {
                foreach ($connectionConfigs['writers'] as &$writerConfig) {
                    $writerConfig['directory'] = preg_replace(
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
