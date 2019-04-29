<?php

namespace Janisbiz\LightOrm\Dms\MySQL\Traits;

trait HavingTrait
{
    public $having = [];

    /**
     * @param string $condition
     * @param array $bind
     *
     * @return $this
     * @throws \Exception
     */
    public function having($condition, array $bind = [])
    {
        if (!$condition) {
            throw new \Exception('You must pass $condition to having function!');
        }

        $this->having[] = $condition;

        if ($bind) {
            $this->bind($bind);
        }

        return $this;
    }
}
