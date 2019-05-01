<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

trait OnDuplicateKeyUpdateTrait
{
    public $onDuplicateKeyUpdate = [];

    /**
     * @param string $onDuplicateKeyUpdate
     * @param array $saveBind
     *
     * @throws \Exception
     * @return $this
     */
    public function onDuplicateKeyUpdate($onDuplicateKeyUpdate, array $saveBind = [])
    {
        if (!$onDuplicateKeyUpdate) {
            throw new \Exception('You must pass $onDuplicateKeyUpdate to onDuplicateKeyUpdate function!');
        }

        $saveBindParsed = [];
        foreach ($saveBind as $placeholder => $value) {
            $newPlaceholder = \sprintf('%s_DK', $placeholder);

            $saveBindParsed[$newPlaceholder] = $value;

            $onDuplicateKeyUpdate = \str_replace(
                \sprintf(':%s', $placeholder),
                \sprintf(':%s', $newPlaceholder),
                $onDuplicateKeyUpdate
            );
        }

        $this->onDuplicateKeyUpdate = \array_merge($this->onDuplicateKeyUpdate, [$onDuplicateKeyUpdate]);
        $this->bindValue = \array_merge($this->bindValue, $saveBindParsed);

        return $this;
    }
}