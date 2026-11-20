<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\Connection;

use Janisbiz\LightOrm\Connection\AbstractConnectionConfig;

class ConnectionConfig extends AbstractConnectionConfig
{
    const ADAPTER = 'mysql';
    const DEFAULT_PORT = 3306;

    public function __construct(
        string $host,
        string $username,
        string $password,
        string $dbname,
        int $port = self::DEFAULT_PORT,
    ) {
        parent::__construct($host, $username, $password, $dbname, static::ADAPTER, $port);
    }

    public function generateDsn(): string
    {
        return sprintf(
            '%s:host=%s;dbname=%s;charset=utf8mb4;port=%d',
            $this->adapter,
            $this->host,
            $this->dbname,
            $this->port
        );
    }

    public function getAdapterConnectionClass(): string
    {
        return Connection::class;
    }
}
