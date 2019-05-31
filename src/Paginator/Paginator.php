<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Paginator;

use Janisbiz\LightOrm\Entity\EntityInterface;
use Janisbiz\LightOrm\QueryBuilder\QueryBuilderInterface;

class Paginator implements PaginatorInterface
{
    const OPTION_SHOW_PAGES_BEFORE_AND_AFTER_CURRENT_PAGE = 'show_pages_before_and_after_current_page';

    const OPTION_VALUE_SHOW_PAGES_BEFORE_AND_AFTER_CURRENT_PAGE_UNLIMITED = 'unlimited';

    /**
     * @var QueryBuilderInterface
     */
    protected $queryBuilder;

    /**
     * @var \Closure
     */
    protected $addPaginateQuery;

    /**
     * @var \Closure
     */
    protected $getPaginateResult;

    /**
     * @var int
     */
    protected $pageSize;

    /**
     * @var int
     */
    protected $currentPage;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var bool
     */
    protected $paginate = true;

    /**
     * @var string
     */
    protected $paginateMethod;

    /**
     * @var array
     */
    protected $result = [];

    /**
     * @var int
     */
    protected $resultTotalCount = 0;

    /**
     * @var int
     */
    protected $resultTotalPages = 0;

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param \Closure $addPaginateQuery
     * @param \Closure $getPaginateResult
     * @param int $pageSize
     * @param int $currentPage
     * @param array $options
     */
    public function __construct(
        QueryBuilderInterface $queryBuilder,
        \Closure $addPaginateQuery,
        \Closure $getPaginateResult,
        int $pageSize,
        int $currentPage = 1,
        array $options = []
    ) {
        $this->queryBuilder = clone $queryBuilder;
        $this->addPaginateQuery = $addPaginateQuery;
        $this->getPaginateResult = $getPaginateResult;
        $this->pageSize = $pageSize;
        $this->currentPage = (int) $currentPage;
        $this->options = \array_merge(
            [
                static::OPTION_SHOW_PAGES_BEFORE_AND_AFTER_CURRENT_PAGE => 2,
            ],
            $options
        );
    }

    /**
     * @param bool $toString
     *
     * @return EntityInterface[]|string
     */
    public function paginate(bool $toString = false)
    {
        if (false === $this->paginate) {
            return $this->result;
        }

        $this->setPaginateMethod(__METHOD__);

        $countQueryBuilder = clone $this->queryBuilder;
        $this->resultTotalCount = $countQueryBuilder->count();

        if (0 < $this->resultTotalCount) {
            $this->resultTotalPages = (int) \ceil($this->resultTotalCount / $this->pageSize);

            if ($this->currentPage > $this->resultTotalPages) {
                $this->currentPage = $this->resultTotalPages;
            }

            $this->paginateFake($toString);
        }

        return $this->result;
    }

    /**
     * Same behavior as paginate method, except executing count to get the total result count!
     *
     * @param bool $toString
     *
     * @return EntityInterface[]|string
     */
    public function paginateFake(bool $toString = false)
    {
        if (false === $this->paginate) {
            return $this->result;
        }

        $this->setPaginateMethod(__METHOD__);

        \call_user_func($this->addPaginateQuery, $this->queryBuilder, $this->pageSize, $this->currentPage);
        $this->result = \call_user_func($this->getPaginateResult, $this->queryBuilder, $toString);
        $this->paginate = false;

        return $this->result;
    }

    /**
     * @return array
     * @throws PaginatorException
     */
    public function getPageNumbers(): array
    {
        if (true === $this->paginate) {
            throw new PaginatorException('You must call "paginate" or "paginateFake" before calling this method!');
        }

        $currentPage = $this->currentPage;
        $resultTotalPages = $this->resultTotalPages;
        if ('paginateFake' === $this->paginateMethod) {
            if (empty($this->result)) {
                $currentPage--;
                $resultTotalPages = $currentPage;
            } else {
                $resultTotalPages = $currentPage + 1;
            }
        }

        $pages = [];

        if ($this->options[static::OPTION_SHOW_PAGES_BEFORE_AND_AFTER_CURRENT_PAGE]
            === static::OPTION_VALUE_SHOW_PAGES_BEFORE_AND_AFTER_CURRENT_PAGE_UNLIMITED
        ) {
            for ($i = 1; $i <= $resultTotalPages; $i++) {
                $pages[$i] = $i;
            }
        } else {
            $pages[$currentPage] = $currentPage;
            for ($i = $this->options[static::OPTION_SHOW_PAGES_BEFORE_AND_AFTER_CURRENT_PAGE]; $i > 0; $i--) {
                if ($currentPage - $i >= 1) {
                    $pages[$currentPage - $i] = ($currentPage - $i);
                }

                if ($currentPage + $i <= $resultTotalPages) {
                    $pages[$currentPage + $i] = ($currentPage + $i);
                }
            }
        }

        \ksort($pages);

        return $pages;
    }

    /**
     * @return int
     * @throws PaginatorException
     */
    public function getTotalPages(): int
    {
        if (true === $this->paginate) {
            throw new PaginatorException('You must call "paginate" or "paginateFake" before calling this method!');
        }

        if ('paginateFake' === $this->paginateMethod) {
            if (empty($this->result)) {
                return $this->currentPage;
            }

            return $this->currentPage + 1;
        }

        return $this->resultTotalPages;
    }

    /**
     * @return int
     * @throws PaginatorException
     */
    public function getCurrentPageNumber(): int
    {
        if (true === $this->paginate) {
            throw new PaginatorException('You must call "paginate" or "paginateFake" before calling this method!');
        }

        return $this->currentPage;
    }

    /**
     * @return null|int
     * @throws PaginatorException
     */
    public function getNextPageNumber(): ?int
    {
        if (true === $this->paginate) {
            throw new PaginatorException('You must call "paginate" or "paginateFake" before calling this method!');
        }

        if ($this->currentPage + 1 <= $this->resultTotalPages || 'paginateFake' === $this->paginateMethod) {
            return $this->currentPage + 1;
        }

        return null;
    }

    /**
     * @return null|int
     * @throws PaginatorException
     */
    public function getPreviousPageNumber(): ?int
    {
        if (true === $this->paginate) {
            throw new PaginatorException('You must call "paginate" or "paginateFake" before calling this method!');
        }

        if (1 <= ($previousPage = $this->currentPage - 1)) {
            return $previousPage;
        }

        return null;
    }

    /**
     * @return int
     * @throws PaginatorException
     */
    public function getResultTotalCount(): int
    {
        if (true === $this->paginate) {
            throw new PaginatorException('You must call "paginate" or "paginateFake" before calling this method!');
        }

        if ('paginateFake' === $this->paginateMethod) {
            throw new PaginatorException(
                'When calling "paginateFake", is is not possible to determine result total count!'
            );
        }

        return $this->resultTotalCount;
    }

    /**
     * @return int
     * @throws PaginatorException
     */
    public function getPageSize(): int
    {
        if (true === $this->paginate) {
            throw new PaginatorException('You must call "paginate" or "paginateFake" before calling this method!');
        }

        return $this->pageSize;
    }

    /**
     * @param string $paginateMethod
     *
     * @return $this
     */
    protected function setPaginateMethod(string $paginateMethod): Paginator
    {
        if (null === $this->paginateMethod) {
            $this->paginateMethod = \explode('::', $paginateMethod)[1];
        }

        return $this;
    }
}
