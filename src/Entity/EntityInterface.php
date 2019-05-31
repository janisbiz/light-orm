<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Entity;

interface EntityInterface
{
    const TABLE_NAME = null;

    /**
     * @param null|string $key
     *
     * @return null|array|string|int|double
     */
    public function &data(?string $key = null);

    /**
     * @param null|string $key
     *
     * @return null|array|string|int|double
     */
    public function &dataOriginal(?string $key = null);

    /**
     * @return bool
     */
    public function isNew(): bool;

    /**
     * @return bool
     */
    public function isSaved(): bool;

    /**
     * @return string[]
     */
    public function primaryKeys(): array;

    /**
     * @return string[]
     */
    public function primaryKeysAutoIncrement(): array;

    /**
     * @return string[]
     */
    public function columns(): array;
}
