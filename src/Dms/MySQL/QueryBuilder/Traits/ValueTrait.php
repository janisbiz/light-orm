<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;

trait ValueTrait
{
    /**
     * @var array
     */
    protected $value = [];

    /**
     * @var array
     */
    protected $bindValue = [];

    /**
     * @param string $column
     * @param null|int|string|double $value
     *
     * @return $this
     */
    public function value($column, $value)
    {
        $columnNormalised = \sprintf(
            '%s_Value',
            \implode(
                '_',
                \array_map(
                    function ($columnPart) {
                        return \mb_convert_case($columnPart, MB_CASE_TITLE);
                    },
                    \explode('.', $column)
                )
            )
        );

        $this->value[$column] = \sprintf(':%s', $columnNormalised);
        $this->bindValue([
            $columnNormalised => $value,
        ]);

        return $this;
    }

    /**
     * @param array $bindValue
     *
     * @return $this
     */
    public function bindValue(array $bindValue = [])
    {
        $this->bindValue = \array_merge($this->bindValue, $bindValue);

        return $this;
    }

    /**
     * @return array
     */
    public function bindValueData()
    {
        return $this->bindValue;
    }

    /**
     * @return null|string
     */
    protected function buildValueQueryPart()
    {
        return empty($this->value)
            ? null
            : \sprintf(
                '(%s) %s (%s)',
                \implode(', ', \array_keys($this->value)),
                ConditionEnum::VALUES,
                \implode(', ', $this->value)
            )
        ;
    }
}
