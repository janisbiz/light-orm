<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface JoinTraitInterface
{
    public function join(string $join, string $tableName, string $onCondition, array $bind = []): static;

    public function joinAs(
        string $join,
        string $tableName,
        string $alias,
        string $onCondition,
        array $bind = []
    ): static;

    public function innerJoin(string $tableName, string $onCondition, array $bind = []): static;

    public function innerJoinAs(string $tableName, string $alias, string $onCondition, array $bind = []): static;

    public function leftJoin(string $tableName, string $onCondition, array $bind = []): static;

    public function leftJoinAs(string $tableName, string $alias, string $onCondition, array $bind = []): static;

    public function rightJoin(string $tableName, string $onCondition, array $bind = []): static;

    public function rightJoinAs(string $tableName, string $alias, string $onCondition, array $bind = []): static;

    public function crossJoin(string $tableName, string $onCondition, array $bind = []): static;

    public function crossJoinAs(string $tableName, string $alias, string $onCondition, array $bind = []): static;

    public function fullOuterJoin(string $tableName, string $onCondition, array $bind = []): static;

    public function fullOuterJoinAs(string $tableName, string $alias, string $onCondition, array $bind = []): static;
}
