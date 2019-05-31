<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Paginator;

use Janisbiz\LightOrm\Entity\EntityInterface;

interface PaginatorInterface
{
    /**
     * @param bool $toString
     *
     * @return EntityInterface[]|string
     */
    public function paginate(bool $toString = false);

    /**
     * Same behavior as paginate method, except executing count to get the total result count!
     *
     * @param bool $toString
     *
     * @return EntityInterface[]|string
     */
    public function paginateFake(bool $toString = false);

    /**
     * @return array
     */
    public function getPageNumbers(): array;

    /**
     * @return int
     */
    public function getTotalPages(): int;

    /**
     * @return int
     */
    public function getCurrentPageNumber(): int;

    /**
     * @return null|int
     */
    public function getNextPageNumber(): ?int;

    /**
     * @return null|int
     */
    public function getPreviousPageNumber(): ?int;

    /**
     * @return int
     */
    public function getResultTotalCount(): int;

    /**
     * @return int
     */
    public function getPageSize(): int;
}
