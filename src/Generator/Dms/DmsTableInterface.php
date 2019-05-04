<?php

namespace Janisbiz\LightOrm\Generator\Dms;

interface DmsTableInterface
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
     * @return DmsColumnInterface[]
     */
    public function getDmsColumns();
}
