<?php

namespace Janisbiz\LightOrm\Dms\MySQL\Traits;

trait ColumnTrait
{
    public $column = [];

    /**
     * @param array|string $column
     * @param boolean $clearAll
     *
     * @throws \Exception
     * @return $this
     */
    public function column($column, $clearAll = false)
    {
        if (empty($column)) {
            throw new \Exception('You must pass $column to column method!');
        }

        if (!is_array($column)) {
            $column = [$column];
        }

        if ($clearAll == true) {
            $this->column = $column;
        } else {
            $this->column = \array_merge($this->column, $column);
        }

        return $this;
    }
}
