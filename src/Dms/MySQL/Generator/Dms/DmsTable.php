<?php

namespace Janisbiz\LightOrm\Dms\MySQL\Generator\Dms;

use Janisbiz\LightOrm\Generator\Dms\DmsColumnInterface;
use Janisbiz\LightOrm\Generator\Dms\DmsTableInterface;

class DmsTable implements DmsTableInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var DmsColumnInterface[]
     */
    protected $dmsColumns = [];

    /**
     * @param string $name
     * @param DmsColumnInterface[] $dmsColumns
     */
    public function __construct($name, array $dmsColumns)
    {
        $this->name = $name;
        $this->dmsColumns = $dmsColumns;
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
     * @return DmsColumnInterface[]
     */
    public function getDmsColumns()
    {
        return $this->dmsColumns;
    }
}
