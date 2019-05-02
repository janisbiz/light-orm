<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\KeywordEnum;

trait OrderByTrait
{
    /**
     * @var array
     */
    protected $orderBy = [];

    /**
     * @param string|array $orderBy
     * @param string $keyword
     *
     * @throws \Exception
     * @return $this
     */
    public function orderBy($orderBy, $keyword = KeywordEnum::ASC)
    {
        if (empty($orderBy)) {
            throw new \Exception('You must pass $orderBy to orderBy method!');
        }

        if (!\in_array($keyword, [KeywordEnum::ASC, KeywordEnum::DESC])) {
            throw new \Exception(\sprintf('Invalid $keyword "%s" for orderBy!', $keyword));
        }

        if (!\is_array($orderBy)) {
            $orderBy = [$orderBy];
        }

        foreach ($orderBy as $orderByColumn) {
            $this->orderBy[] = \sprintf('%s %s', $orderByColumn, $keyword);
        }

        return $this;
    }
}