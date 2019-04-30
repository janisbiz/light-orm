<?php

namespace Janisbiz\LightOrm\Connection;

use Janisbiz\LightOrm\Dms\MySQL\Connection\Connection;

class ConnectionConfig
{
    const ADAPTER_MYSQL = 'mysql';

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $dbname;

    /**
     * @var string
     */
    private $adapter;

    /**
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $dbname
     * @param string $adapter
     */
    public function __construct($host, $username, $password, $dbname, $adapter)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
        $this->adapter = $this->validateAdapter($adapter);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function generateDsn()
    {
        switch ($this->adapter) {
            case self::ADAPTER_MYSQL:
                return \sprintf(
                    '%s:host=%s;dbname=%s;charset=utf8mb4',
                    $this->adapter,
                    $this->host,
                    $this->dbname
                );
        }

        throw new \Exception('Could not build DSN!');
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getDbname()
    {
        return $this->dbname;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getAdapterConnectionClass()
    {
        switch ($this->adapter) {
            case self::ADAPTER_MYSQL:
                return Connection::class;
        }

        throw new \Exception('Could not get adapter connection class!');
    }

    /**
     * @param string $adapter
     *
     * @throws \InvalidArgumentException
     * @return string
     */
    private function validateAdapter($adapter)
    {
        $adapter = \mb_strtolower($adapter);

        switch ($adapter) {
            case self::ADAPTER_MYSQL:
                return $adapter;
        }

        throw new \InvalidArgumentException(\sprintf(
            'Invalid adapter "%s"! Supported adapters: "%s"',
            $adapter,
            \implode(
                '", "',
                [
                    self::ADAPTER_MYSQL,
                ]
            )
        ));
    }
}
