<?php

namespace Janisbiz\LightOrm\Entity;

interface EntityInterface
{
    const TABLE_NAME = null;

    /**
     * @return array
     */
    public function data();

    /**
     * @return array
     */
    public function dataOriginal();

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
