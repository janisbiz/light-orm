<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

trait BindTrait
{
    /**
     * @var array
     */
    protected $bind = [];

    /**
     * @param array $bind
     *
     * @return $this
     */
    public function bind(array $bind = [])
    {
        $this->bind = \array_merge($this->bind, $bind);

        return $this;
    }

    /**
     * @return array
     */
    public function bindData(): array
    {
        return $this->bind;
    }
}
