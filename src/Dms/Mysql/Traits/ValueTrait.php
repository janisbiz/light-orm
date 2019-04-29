<?php

namespace Janisbiz\LightOrm\Dms\MySQL\Traits;

trait ValueTrait
{
    /**
     * @var array
     */
    public $value = [];

    /**
     * @var array
     */
    public $bindValue = [];

    /**
     * @param string $column
     * @param null|int|string $value
     *
     * @return $this
     */
    public function value($column, $value)
    {
        $columnNormalised = \sprintf(
            '%s_Insert',
            \implode(
                '',
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
}
