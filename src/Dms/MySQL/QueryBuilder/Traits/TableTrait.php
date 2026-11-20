<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;

trait TableTrait
{
    protected array $table = [];

    /**
     * @throws QueryBuilderException
     */
    public function table(array|string $table, bool $clearAll = false): static
    {
        if (empty($table)) {
            throw new QueryBuilderException('You must pass $table to table method!');
        }

        if (!is_array($table)) {
            $table = [$table];
        }

        if ($clearAll == true) {
            $this->table = $table;
        } else {
            $this->table = array_merge($this->table, $table);
        }

        return $this;
    }

    protected function buildTableQueryPart(): ?string
    {
        return reset($this->table) ?: null;
    }

    protected function buildFromQueryPart(): ?string
    {
        return empty($this->table)
            ? null
            : sprintf('%s %s', ConditionEnum::FROM, implode(', ', $this->table))
        ;
    }
}
