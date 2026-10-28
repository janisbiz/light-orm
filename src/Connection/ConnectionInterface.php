<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Connection;

use PDO;
use PDOStatement;

interface ConnectionInterface
{
    public function prepare(string $query, array $options = []): PDOStatement|false;

    public function commit(): bool;

    public function rollBack(): bool;

    public function inTransaction(): bool;

    public function setAttribute(int $attribute, mixed $value): bool;

    public function exec(string $statement): int|false;

    public function query(string $query, ?int $fetchMode, mixed ...$fetchModeArgs);

    public function lastInsertId(?string $name): string|false;

    public function errorCode(): ?string;

    public function errorInfo(): array;

    public function getAttribute(int $attribute): mixed;

    public function quote(string $string, int $type = PDO::PARAM_STR): string|false;

    public function beginTransaction(): self;
}
