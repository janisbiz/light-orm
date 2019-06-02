<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;

trait TableTrait
{
    /**
     * @var string[]
     */
    protected $table = [];

    /**
     * @param string[]|string $table
     * @param boolean $clearAll
     *
     * @return $this
     * @throws QueryBuilderException
     */
    public function table($table, $clearAll = false)
    {
        if (empty($table)) {
            throw new QueryBuilderException('You must pass $table to table method!');
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

    /**
     * @return string
     */
    protected function buildTableQueryPart()
    {
        return \reset($this->table);
    }

    /**
     * @return null|string
     */
    protected function buildFromQueryPart()
    {
        return empty($this->table)
            ? null
            : \sprintf('%s %s', ConditionEnum::FROM, \implode(', ', $this->table))
        ;
    }
}
