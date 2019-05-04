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

        $bindValuePlaceholder = \sprintf('%s_OnDuplicateKeyUpdate', $column);

        $this->onDuplicateKeyUpdate = \array_merge(
            $this->onDuplicateKeyUpdate,
            [
                \sprintf('%s = :%s', $column, $bindValuePlaceholder),
            ]
        );

        $this->bindValue([
            $bindValuePlaceholder => $value,
        ]);

        return $this;
    }
}
