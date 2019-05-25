<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;

trait LimitOffsetTrait
{
    /**
     * @var null|int
     */
    protected $limit;

    /**
     * @var null|int
     */
    protected $offset;

    /**
     * @param int $limit
     * @param int $offset
     *
     * @return $this
     */
    public function limitWithOffset($limit, $offset)
    {
        return $this->limit($limit)->offset($offset);
    }

    /**
     * @param int $offset
     *
     * @return $this
     * @throws QueryBuilderException
     */
    public function offset($offset)
    {
        if (empty($offset)) {
            throw new QueryBuilderException('You must pass $offset to offset method!');
        }

        if (empty($this->limit)) {
            throw new QueryBuilderException('You must set LIMIT before calling offset method!');
        }

        $this->offset = (int) $offset;

        return $this;
    }

    /**
     * @param int $limit
     *
     * @return $this
     * @throws QueryBuilderException
     */
    public function limit($limit)
    {
        if (empty($limit)) {
            throw new QueryBuilderException('You must pass $limit to limit method!');
        }

        $this->limit = (int) $limit;

        return $this;
    }

    /**
     * @return null|string
     */
    protected function buildLimitQueryPart()
    {
        return empty($this->limit) ? null : \sprintf('%s %d', ConditionEnum::LIMIT, $this->limit);
    }

    /**
     * @return null|string
     */
    protected function buildOffsetQueryPart()
    {
        return empty($this->offset) ? null : \sprintf('%s %d', ConditionEnum::OFFSET, $this->offset);
    }
}
