<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

trait SetTrait
{
    public $set = [];

    /**
     * @param string $column
     * @param null|int|string $value
     *
     * @return $this
     */
    public function set($column, $value)
    {
        $columnNormalised = \sprintf(
            '%s_Update',
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

        $this->set[$column] = \sprintf('%s = :%s', $column, $columnNormalised);
        $this->bind([
            $columnNormalised => $value,
        ]);

        return $this;
    }
}
