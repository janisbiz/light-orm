<?php

namespace Janisbiz\LightOrm\Generator\Dms;

interface DmsDatabaseInterface
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
     * @return DmsTableInterface[]
     */
    public function getDmsTables();
}
