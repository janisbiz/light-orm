<?php

namespace Janisbiz\LightOrm\MySQL\Traits;

trait FromTrait
{
    public $from = [];

    /**
     * @param array|string $from
     * @param boolean $clearAll
     *
     * @throws \Exception
     * @return $this
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
