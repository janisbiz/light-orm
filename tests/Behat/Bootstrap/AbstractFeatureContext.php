<?php

namespace Janisbiz\LightOrm\Tests\Behat\Bootstrap;

use Behat\Behat\Context\Context;
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
    protected function getConnectionConfig($connectionName = null)
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
    protected function getWritersConfig($connectionName)
    {
        if (!isset($this->getConfig()['generator'][$connectionName]['writers'])) {
            throw new \Exception(\sprintf('Could not get writer config for connection "%s"', $connectionName));
        }

        return $this->getConfig()['generator'][$connectionName]['writers'];
    }

    /**
     * @return array
     */
    private function getConfig()
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
