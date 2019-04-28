<?php

namespace Janisbiz\LightOrm\MySQL\Traits;

use Janisbiz\LightOrm\MySQL\Enums\KeywordEnum;

trait OrderTrait
{
    public $orderBy = [];

    /**
     * @param string|array $orderBy
     * @param string $criteria
     *
     * @throws \Exception
     * @return $this
     */
    public function orderBy($orderBy, $criteria = KeywordEnum::ASC)
    {
        if (empty($orderBy)) {
            throw new \Exception('You must pass $orderBy to orderBy method!');
        }

        if (!\in_array($criteria, [KeywordEnum::ASC, KeywordEnum::DESC])) {
            throw new \Exception(\sprintf('Invalid $criteria "%s" for orderBy!', $criteria));
        }

        if (!is_array($orderBy)) {
            $orderBy = [$orderBy];
        }

        foreach ($orderBy as $orderByColumn) {
            $this->orderBy[] = \sprintf('%s %s', $orderByColumn, $criteria);
        }

        return $this;
    }
}
