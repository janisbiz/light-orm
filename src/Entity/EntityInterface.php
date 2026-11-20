<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Entity;

interface EntityInterface
{
    const TABLE_NAME = null;

    public function &data(?string $key = null): array|string|int|float|null;

    public function &dataOriginal(?string $key = null): array|string|int|float|null;

    public function isNew(): bool;

    public function isSaved(): bool;

    public function primaryKeys(): array;

    public function primaryKeysAutoIncrement(): array;

    public function columns(): array;
}
