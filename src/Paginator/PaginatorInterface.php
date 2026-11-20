<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Paginator;

use Janisbiz\LightOrm\Entity\EntityInterface;

interface PaginatorInterface
{
    /**
     * @return EntityInterface[]|string
     */
    public function paginate(bool $toString = false): string|array;

    /**
     * Same behavior as paginate method, except executing count to get the total result count!
     *
     * @return EntityInterface[]|string
     */
    public function paginateFake(bool $toString = false): string|array;

    public function getPageNumbers(): array;

    public function getTotalPages(): int;

    public function getCurrentPageNumber(): int;

    public function getNextPageNumber(): ?int;

    public function getPreviousPageNumber(): ?int;

    public function getResultTotalCount(): int;

    public function getPageSize(): int;
}
