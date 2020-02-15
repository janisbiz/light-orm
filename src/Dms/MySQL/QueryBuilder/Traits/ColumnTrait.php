<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\TraitsInterface;

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
     * @return $this|TraitsInterface
     * @throws QueryBuilderException
     */
    public function column($column, bool $clearAll = false)
    {
        if (empty($column)) {
            throw new QueryBuilderException('You must pass $column to column method!');
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
    protected function buildColumnQueryPart(): string
    {
        return empty($this->column) ? '*' : \implode(', ', $this->column);
    }
}
