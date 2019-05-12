<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

trait ColumnTrait
{
    /**
     * @var array
     */
    protected $column = [];

    /**
     * @param array|string $column
     * @param boolean $clearAll
     *
     * @return $this
     * @throws \Exception
     */
    public function column($column, $clearAll = false)
    {
        if (empty($column)) {
            throw new \Exception('You must pass $column to column method!');
        }

        if (!\is_array($column)) {
            $column = [$column];
        }

        if ($clearAll == true) {
            $this->column = $column;
        } else {
            $this->column = \array_merge($this->column, $column);
        }

        return $this;
    }

    /**
     * @return string
     */
    protected function buildColumnQueryPart()
    {
        return empty($this->column) ? '*' : \implode(', ', $this->column);
    }
}
