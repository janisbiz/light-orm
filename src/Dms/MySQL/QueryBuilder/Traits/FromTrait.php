<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

trait FromTrait
{
    /**
     * @var array
     */
    protected $from = [];

    /**
     * @param array|string $from
     * @param boolean $clearAll
     *
     * @return $this
     * @throws \Exception
     */
    public function from($from, $clearAll = false)
    {
        if (empty($from)) {
            throw new \Exception('You must pass $from to from method!');
        }

        if (!\is_array($from)) {
            $from = [$from];
        }

        if ($clearAll == true) {
            $this->from = $from;
        } else {
            $this->from = \array_merge($this->from, $from);
        }

        return $this;
    }
}
