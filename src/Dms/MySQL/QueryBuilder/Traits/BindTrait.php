<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

trait BindTrait
{
    protected array $bind = [];

    public function bind(array $bind = []): static
    {
        $this->bind = array_merge($this->bind, $bind);

        return $this;
    }

    public function bindData(): array
    {
        return $this->bind;
    }
}
