<?php

namespace Janisbiz\LightOrm\Generator\Dms;

interface DmsColumnInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getPhpName();

    /**
     * @return string
     */
    public function getType();

    /**
     * @throws DmsException
     * @return string
     */
    public function getPhpType();

    /**
     * @return bool
     */
    public function isNullable();

    /**
     * @return string
     */
    public function getKey();

    /**
     * @return string
     */
    public function getDefault();

    /**
     * @return string
     */
    public function getPhpDefaultType();

    /**
     * @return null|string
     */
    public function getExtra();
}
