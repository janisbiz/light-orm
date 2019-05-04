<?php

namespace Janisbiz\LightOrm\Generator\Dms;

class DmsDatabase
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var DmsTable[]
     */
    private $tables = [];

    /**
     * @param $name
     * @param $tables
     */
    public function __construct($name, $tables)
    {
        $this->name = $name;
        $this->tables = $tables;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPhpName()
    {
        return \ucfirst(\preg_replace_callback(
            '/[^a-z0-9]+(?<name>\w{1})/i',
            function ($matches) {
                return \strtoupper($matches['name']);
            },
            $this->getName()
        ));
    }

    /**
     * @return DmsTable[]
     */
    public function getTables()
    {
        return $this->tables;
    }
}
