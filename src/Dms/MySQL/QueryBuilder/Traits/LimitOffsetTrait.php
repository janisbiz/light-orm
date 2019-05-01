<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

trait LimitOffsetTrait
{
    public $limit;
    public $offset;

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
     * @throws \Exception
     */
    public function offset($offset)
    {
        if (!isset($offset)) {
            throw new \Exception('You must pass $offset to offset method!');
        }

        if (!isset($this->limit)) {
            throw new \Exception('You must set LIMIT before calling offset method!');
        }

        $this->offset = (int) $offset;

        return $this;
    }

    /**
     * @param int $limit
     *
     * @return $this
     * @throws \Exception
     */
    public function limit($limit)
    {
        if (!isset($limit)) {
            throw new \Exception('You must pass $limit to limit method!');
        }

        $this->limit = (int) $limit;

        return $this;
    }
}
