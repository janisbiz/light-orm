<?php declare(strict_types=1);

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
     * @param bool $clearAll
     *
     * @return $this
     * @throws QueryBuilderException
     */
    public function table($table, bool $clearAll = false)
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
     * @return null|string
     */
    protected function buildTableQueryPart(): ?string
    {
        return \reset($this->table) ?: null;
    }

    /**
     * @return null|string
     */
    protected function buildFromQueryPart(): ?string
    {
        return empty($this->table)
            ? null
            : \sprintf('%s %s', ConditionEnum::FROM, \implode(', ', $this->table))
        ;
    }
}
