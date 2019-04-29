<?php

namespace Janisbiz\LightOrm\Connection;

interface ConnectionInterface
{
    /**
     * @return $this
     */
    public function beginTransaction();
}
