<?php

namespace Janisbiz\LightOrm\Connection;

interface ConnectionConfigInterface
{
    /**
     * @return string
     */
    public function generateDsn();

    /**
     * @return string
     */
    public function getUsername();

    /**
     * @return string
     */
    public function getPassword();

    /**
     * @return string
     */
    public function getDbname();

    /**
     * @return string
     */
    public function getAdapterConnectionClass();
}
