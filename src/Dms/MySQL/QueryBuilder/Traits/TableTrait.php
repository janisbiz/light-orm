<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

trait TableTrait
{
    /**
     * @var array
     */
    protected $table = [];

    /**
     * @param array|string $table
     * @param boolean $clearAll
     *
     * @return $this
     * @throws \Exception
     */
    public function table($table, $clearAll = false)
    {
        if (empty($table)) {
            throw new \Exception('You must pass $table to table method!');
        }

        if (!\is_array($table)) {
            $table = [$table];
        }

        if ($clearAll == true) {
            $this->table = $table;
        } else {
            $this->table = \array_merge($this->table, $table);
        }

        return $this;
    }
}
