<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\TraitsInterface;

trait SetTrait
{
    /**
     * @var array
     */
    protected $set = [];

    /**
     * @param string $column
     * @param null|int|string $value
     *
     * @return $this|TraitsInterface
     */
    public function set($column, $value)
    {
        $columnNormalised = \sprintf(
            '%s_Update',
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

        $this->set[$column] = \sprintf('%s = :%s', $column, $columnNormalised);
        $this->bind([
            $columnNormalised => $value,
        ]);

        return $this;
    }

    /**
     * @return null|string
     */
    protected function buildSetQueryPart()
    {
        return empty($this->set)
            ? null
            : \sprintf('%s %s', ConditionEnum::SET, \implode(', ', \array_unique($this->set)))
        ;
    }
}
