<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;

trait ColumnTrait
{
    protected array $column = [];

    /**
     * @throws QueryBuilderException
     */
    public function column(array|string $column, bool $clearAll = false): static
    {
        if (empty($column)) {
            throw new QueryBuilderException('You must pass $column to column method!');
        }

        if (!is_array($column)) {
            $column = [$column];
        }

        if ($clearAll == true) {
            $this->column = $column;
        } else {
            $this->column = array_merge($this->column, $column);
        }

        return $this;
    }

    protected function buildColumnQueryPart(): string
    {
        return empty($this->column) ? '*' : implode(', ', $this->column);
    }
}
