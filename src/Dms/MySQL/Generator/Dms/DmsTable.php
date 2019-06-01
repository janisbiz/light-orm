<?php declare(strict_types=1);

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
    public function __construct(string $name, array $dmsColumns)
    {
        $this->name = $name;
        $this->dmsColumns = $dmsColumns;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPhpName(): string
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
    public function getDmsColumns(): array
    {
        return $this->dmsColumns;
    }
}
