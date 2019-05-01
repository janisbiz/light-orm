<?php

namespace Janisbiz\LightOrm\Generator\Dms;

class Table
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Column[]
     */
    private $columns = [];

    /**
     * @param string $name
     * @param array $columns
     */
    public function __construct($name, array $columns)
    {
        $this->name = $name;
        $this->columns = $columns;
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
     * @return Column[]
     */
    public function getColumns()
    {
        return $this->columns;
    }
}
