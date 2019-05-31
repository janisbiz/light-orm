<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Connection;

abstract class AbstractConnectionConfig implements ConnectionConfigInterface
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $dbname;

    /**
     * @var string
     */
    protected $adapter;

    /**
     * @var int
     */
    protected $port;

    /**
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $dbname
     * @param string $adapter
     * @param int $port
     */
    public function __construct(
        string $host,
        string $username,
        string $password,
        string $dbname,
        string $adapter,
        int $port = 3306
    ) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
        $this->adapter = $adapter;
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getDbname(): string
    {
        return $this->dbname;
    }
}
