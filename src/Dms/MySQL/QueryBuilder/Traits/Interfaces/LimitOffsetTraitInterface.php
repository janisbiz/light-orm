<?php declare(strict_types=1);

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
    public function limitWithOffset(int $limit, int $offset);

    /**
     * @param int $offset
     *
     * @return $this|TraitsInterface
     */
    public function offset(int $offset);

    /**
     * @param int $limit
     *
     * @return $this|TraitsInterface
     */
    public function limit(int $limit);
}
