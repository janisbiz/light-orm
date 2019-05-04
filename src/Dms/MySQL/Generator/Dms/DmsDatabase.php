<?php

namespace Janisbiz\LightOrm\Dms\MySQL\Generator\Dms;

use Janisbiz\LightOrm\Generator\Dms\DmsDatabaseInterface;
use Janisbiz\LightOrm\Generator\Dms\DmsTableInterface;

class DmsDatabase implements DmsDatabaseInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var DmsTableInterface[]
     */
    protected $dmsTables = [];

    /**
     * @param string $name
     * @param DmsTableInterface[] $dmsTables
     */
    public function __construct($name, $dmsTables)
    {
        $this->name = $name;
        $this->dmsTables = $dmsTables;
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
     * @return DmsTableInterface[]
     */
    public function getDmsTables()
    {
        return $this->dmsTables;
    }
}
