<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Connection;

abstract class AbstractConnectionConfig implements ConnectionConfigInterface
{
    public function __construct(
        protected string $host,
        protected string $username,
        protected string $password,
        protected string $dbname,
        protected string $adapter,
        protected int $port
    ) {
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getDbname(): string
    {
        return $this->dbname;
    }
}
