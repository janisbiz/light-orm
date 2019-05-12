<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

trait OnDuplicateKeyUpdateTrait
{
    /**
     * @var array
     */
    protected $onDuplicateKeyUpdate = [];

    /**
     * @param string $column
     * @param null|int|string|double $value
     *
     * @throws \Exception
     * @return $this
     */
    public function onDuplicateKeyUpdate($column, $value)
    {
        if (empty($column)) {
            throw new \Exception('You must pass $column to onDuplicateKeyUpdate function!');
        }

        $columnNormalised = \sprintf(
            '%s_OnDuplicateKeyUpdate',
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

        $this->onDuplicateKeyUpdate = \array_merge(
            $this->onDuplicateKeyUpdate,
            [
                $column => \sprintf('%s = :%s', $column, $columnNormalised),
            ]
        );

        $this->bindValue([
            $columnNormalised => $value,
        ]);

        return $this;
    }

    /**
     * @return null|string
     */
    protected function buildOnDuplicateKeyUpdateQueryPart()
    {
        return empty($this->onDuplicateKeyUpdate)
            ? null
            : \sprintf('ON DUPLICATE KEY UPDATE %s', \implode(', ', $this->onDuplicateKeyUpdate))
        ;
    }
}
