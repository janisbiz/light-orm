<?php

namespace Janisbiz\LightOrm\Entity;

interface EntityInterface
{
    const TABLE_NAME = null;

    /**
     * @param null|string $key
     *
     * @return null|array|string|int|double
     */
    public function &data($key = null);

    /**
     * @param null|string $key
     *
     * @return null|array|string|int|double
     */
    public function &dataOriginal($key = null);

    /**
     * @return bool
     */
    public function isNew();

    /**
     * @return bool
     */
    public function isSaved();

    /**
     * @return string[]
     */
    public function primaryKeys();

    /**
     * @return string[]
     */
    public function primaryKeysAutoIncrement();

    /**
     * @return string[]
     */
    public function columns();
}
