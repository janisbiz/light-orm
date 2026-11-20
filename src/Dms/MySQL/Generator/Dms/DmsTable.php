<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\Generator\Dms;

use Janisbiz\LightOrm\Generator\Dms\DmsColumnInterface;
use Janisbiz\LightOrm\Generator\Dms\DmsTableInterface;

class DmsTable implements DmsTableInterface
{
    /**
     * @param DmsColumnInterface[] $dmsColumns
     */
    public function __construct(
        protected string $name,
        protected array $dmsColumns
    ) {
        (static fn(DmsColumnInterface ...$dmsColumns) => $dmsColumns)(...$this->dmsColumns);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPhpName(): string
    {
        return ucfirst(preg_replace_callback(
            '/[^a-z0-9]+(?<name>\w{1})/i',
            fn($matches) => strtoupper($matches['name']),
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
