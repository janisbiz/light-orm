<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Interfaces;

interface LimitOffsetTraitInterface
{
    /**
     * @param int $limit
     * @param int $offset
     *
     * @return $this
     */
    public function limitWithOffset(int $limit, int $offset);

    /**
     * @param int $offset
     *
     * @return $this
     */
    public function offset(int $offset);

    /**
     * @param int $limit
     *
     * @return $this
     */
    public function limit(int $limit);
}
