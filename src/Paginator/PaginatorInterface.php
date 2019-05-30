<?php

namespace Janisbiz\LightOrm\Paginator;

use Janisbiz\LightOrm\Entity\EntityInterface;

interface PaginatorInterface
{
    /**
     * @param bool $toString
     *
     * @return EntityInterface[]|string
     */
    public function paginate($toString = false);

    /**
     * Same behavior as paginate method, except executing count to get the total result count!
     *
     * @param bool $toString
     *
     * @return EntityInterface[]|string
     */
    public function paginateFake($toString = false);

    /**
     * @return array
     */
    public function getPageNumbers();

    /**
     * @return int
     */
    public function getTotalPages();

    /**
     * @return int
     */
    public function getCurrentPageNumber();

    /**
     * @return null|int
     */
    public function getNextPageNumber();

    /**
     * @return null|int
     */
    public function getPreviousPageNumber();

    /**
     * @return int
     */
    public function getResultTotalCount();

    /**
     * @return int
     */
    public function getPageSize();
}
