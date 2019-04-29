<?php

namespace Janisbiz\LightOrm\Generator\Dms;

class Database
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Table[]
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
            '/\_(?<name>\w{1})/i',
            function ($matches) {
                return \strtoupper($matches['name']);
            },
            $this->getName()
        ));
    }

    /**
     * @return Table[]
     */
    public function getTables()
    {
        return $this->tables;
    }
}
