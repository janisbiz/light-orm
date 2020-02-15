<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\TraitsInterface;

interface LimitOffsetTraitInterface
{
    /**
     * @param int $limit
     * @param int $offset
     *
     * @return $this|TraitsInterface
     */
    public function limitWithOffset($limit, $offset);

    /**
     * @param int $offset
     *
     * @return $this|TraitsInterface
     */
    public function offset($offset);

    /**
     * @param int $limit
     *
     * @return $this|TraitsInterface
     */
    public function limit($limit);
}
