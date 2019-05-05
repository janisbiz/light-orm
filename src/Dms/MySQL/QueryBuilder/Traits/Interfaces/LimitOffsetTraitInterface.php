<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface LimitOffsetTraitInterface
{
    /**
     * @param int $limit
     * @param int $offset
     *
     * @return $this
     */
    public function limitWithOffset($limit, $offset);

    /**
     * @param int $offset
     *
     * @return $this
     */
    public function offset($offset);

    /**
     * @param int $limit
     *
     * @return $this
     */
    public function limit($limit);
}
