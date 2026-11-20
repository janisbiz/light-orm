<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\Generator\Dms;

use Janisbiz\LightOrm\Generator\Dms\DmsDatabaseInterface;
use Janisbiz\LightOrm\Generator\Dms\DmsTableInterface;

class DmsDatabase implements DmsDatabaseInterface
{
    /**
     * @param DmsTableInterface[] $dmsTables
     */
    public function __construct(
        protected string $name,
        protected array $dmsTables
    ) {
        (static fn(DmsTableInterface ...$dmsTables) => $dmsTables)(...$this->dmsTables);
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
     * @return DmsTableInterface[]
     */
    public function getDmsTables(): array
    {
        return $this->dmsTables;
    }
}
