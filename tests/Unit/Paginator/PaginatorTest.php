<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Paginator;

use Janisbiz\LightOrm\Entity\EntityInterface;
use Janisbiz\LightOrm\Paginator\Paginator;
use Janisbiz\LightOrm\Paginator\PaginatorException;
use Janisbiz\LightOrm\QueryBuilder\QueryBuilderInterface;
use Janisbiz\LightOrm\Repository\AbstractRepository;
use Janisbiz\LightOrm\Repository\RepositoryInterface;
use Janisbiz\LightOrm\Tests\Unit\ReflectionTrait;
use PHPUnit\Framework\TestCase;

class PaginatorTest extends TestCase
{
    use ReflectionTrait;

    const PAGE_SIZE = 10;
    const PREVIOUS_PAGE_NON_EXISTENT = null;
    const NEXT_PAGE_NON_EXISTENT = null;
    const CURRENT_PAGE = 4;
    const CURRENT_PAGE_LOW = 1;
    const RESULT_COUNT = 80;
    const RESULT_COUNT_LOW = 10;
    const RESULT_COUNT_ODD = 81;

    /**
     * @var EntityInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entity;

    /**
     * @var QueryBuilderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queryBuilder;

    /**
     * @var RepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $abstractRepository;

    /**
     * @var \Closure
     */
    private $addPaginateQuery;

    /**
     * @var \Closure
     */
    private $getPaginateResult;

    public function setUp()
    {
        $this->entity = $this->createMock(EntityInterface::class);
        $this->queryBuilder = $this->createMock(QueryBuilderInterface::class);
        $this->abstractRepository = $this->getMockForAbstractClass(
            AbstractRepository::class,
            [],
            '',
            true,
            true,
            true,
            [
                'addPaginateQuery',
                'getPaginateResult',
            ]
        );
    }

    public function testConstruct()
    {
        $paginator = $this->createPaginator();

        $this->assertEquals(
            $this->queryBuilder,
            $this->createAccessibleProperty($paginator, 'queryBuilder')->getValue($paginator)
        );
        $this->assertEquals(
            $this->addPaginateQuery,
            $this->createAccessibleProperty($paginator, 'addPaginateQuery')->getValue($paginator)
        );
        $this->assertEquals(
            $this->getPaginateResult,
            $this->createAccessibleProperty($paginator, 'getPaginateResult')->getValue($paginator)
        );
        $this->assertEquals(
            static::PAGE_SIZE,
            $this->createAccessibleProperty($paginator, 'pageSize')->getValue($paginator)
        );
        $this->assertEquals(
            static::CURRENT_PAGE,
            $this->createAccessibleProperty($paginator, 'currentPage')->getValue($paginator)
        );
    }

    /**
     * @return Paginator
     */
    public function testPaginate()
    {
        $this->queryBuilder->method('count')->willReturn(static::RESULT_COUNT);
        $this
            ->abstractRepository
            ->expects($this->once())
            ->method('addPaginateQuery')
            ->withConsecutive([$this->queryBuilder, static::PAGE_SIZE, static::CURRENT_PAGE])
        ;

        $resultExpected = [];
        for ($i = 1; $i <= static::PAGE_SIZE; $i++) {
            $resultExpected[] = $this->entity;
        }
        $this
            ->abstractRepository
            ->expects($this->once())
            ->method('getPaginateResult')
            ->withConsecutive([$this->queryBuilder, false])
            ->willReturn($resultExpected)
        ;

        $paginator = $this->createPaginator();
        $result = $paginator->paginate();

        $this->assertCount(\count($resultExpected), $result);
        $this->assertEquals($resultExpected, $result);

        $paginator->paginate();

        return $paginator;
    }

    /**
     * @return Paginator
     */
    public function testPaginateWhenCurrentPageIsHigherThanExistingPage()
    {
        $this->queryBuilder->method('count')->willReturn(static::RESULT_COUNT_LOW);
        $this
            ->abstractRepository
            ->expects($this->once())
            ->method('addPaginateQuery')
            ->withConsecutive([$this->queryBuilder, static::PAGE_SIZE, static::CURRENT_PAGE_LOW])
        ;

        $resultExpected = [];
        for ($i = 1; $i <= static::PAGE_SIZE; $i++) {
            $resultExpected[] = $this->entity;
        }
        $this
            ->abstractRepository
            ->expects($this->once())
            ->method('getPaginateResult')
            ->withConsecutive([$this->queryBuilder, false])
            ->willReturn($resultExpected)
        ;

        $paginator = $this->createPaginator();
        $result = $paginator->paginate();

        $this->assertCount(\count($resultExpected), $result);
        $this->assertEquals($resultExpected, $result);

        return $paginator;
    }

    /**
     * @return Paginator
     */
    public function testPaginateFake()
    {
        $this
            ->abstractRepository
            ->expects($this->once())
            ->method('addPaginateQuery')
            ->withConsecutive([$this->queryBuilder, static::PAGE_SIZE, static::CURRENT_PAGE])
        ;

        $resultExpected = [];
        for ($i = 1; $i <= static::PAGE_SIZE; $i++) {
            $resultExpected[] = $this->entity;
        }
        $this
            ->abstractRepository
            ->expects($this->once())
            ->method('getPaginateResult')
            ->withConsecutive([$this->queryBuilder, false])
            ->willReturn($resultExpected)
        ;

        $paginator = $this->createPaginator();
        $result = $paginator->paginateFake();

        $this->assertCount(\count($resultExpected), $result);
        $this->assertEquals($resultExpected, $result);

        $paginator->paginateFake();

        return $paginator;
    }

    /**
     * @return Paginator
     */
    public function testPaginateFakeWithoutResult()
    {
        $this
            ->abstractRepository
            ->expects($this->once())
            ->method('addPaginateQuery')
            ->withConsecutive([$this->queryBuilder, static::PAGE_SIZE, static::CURRENT_PAGE])
        ;

        $resultExpected = [];
        $this
            ->abstractRepository
            ->expects($this->once())
            ->method('getPaginateResult')
            ->withConsecutive([$this->queryBuilder, false])
            ->willReturn($resultExpected)
        ;

        $paginator = $this->createPaginator();
        $result = $paginator->paginateFake();

        $this->assertCount(\count($resultExpected), $result);
        $this->assertEquals($resultExpected, $result);

        return $paginator;
    }

    public function testGetPageNumbers()
    {
        $paginator = $this->testPaginate();
        $pagesBeforeAfter = $this
            ->createAccessibleProperty($paginator, 'options')
            ->getValue($paginator)[Paginator::OPTION_SHOW_PAGES_BEFORE_AND_AFTER_CURRENT_PAGE]
        ;

        $pages = \range(static::CURRENT_PAGE - $pagesBeforeAfter, static::CURRENT_PAGE + $pagesBeforeAfter);
        $this->assertEquals(\array_combine($pages, $pages), $paginator->getPageNumbers());
    }

    public function testGetPageNumbersWithUnlimitedOption()
    {
        $paginator = $this->testPaginate();
        $this
            ->createAccessibleProperty($paginator, 'options')
            ->setValue(
                $paginator,
                [
                    Paginator::OPTION_SHOW_PAGES_BEFORE_AND_AFTER_CURRENT_PAGE =>
                        Paginator::OPTION_VALUE_SHOW_PAGES_BEFORE_AND_AFTER_CURRENT_PAGE_UNLIMITED,
                ]
            )
        ;

        $totalPages = static::RESULT_COUNT / static::PAGE_SIZE;
        $pages = \range($totalPages - $totalPages + 1, $totalPages);
        $this->assertEquals(\array_combine($pages, $pages), $paginator->getPageNumbers());
    }

    public function testGetPageNumbersWhenPaginateFake()
    {
        $paginator = $this->testPaginateFake();
        $pagesBeforeAfter = $this
            ->createAccessibleProperty($paginator, 'options')
            ->getValue($paginator)[Paginator::OPTION_SHOW_PAGES_BEFORE_AND_AFTER_CURRENT_PAGE]
        ;

        $totalPages = static::CURRENT_PAGE;
        $pages = \range($totalPages - $pagesBeforeAfter, $totalPages + 1);
        $this->assertEquals(\array_combine($pages, $pages), $paginator->getPageNumbers());
    }

    public function testGetPageNumbersWhenPaginateFakeWithoutResult()
    {
        $paginator = $this->testPaginateFakeWithoutResult();
        $pagesBeforeAfter = $this
            ->createAccessibleProperty($paginator, 'options')
            ->getValue($paginator)[Paginator::OPTION_SHOW_PAGES_BEFORE_AND_AFTER_CURRENT_PAGE]
        ;

        $totalPages = static::CURRENT_PAGE - 1;
        $pages = \range($totalPages - $pagesBeforeAfter, $totalPages);

        $this->assertEquals(\array_combine($pages, $pages), $paginator->getPageNumbers());
    }

    public function testGetPageNumbersWhenNotPaginated()
    {
        $this->expectException(PaginatorException::class);
        $this->expectExceptionMessage('You must call "paginate" or "paginateFake" before calling this method!');

        $this->createPaginator()->getPageNumbers();
    }

    /**
     * @dataProvider getTotalPagesData
     *
     * @param int $resultCount
     * @param int $expectedTotalPages
     */
    public function testGetTotalPages(int $resultCount, int $expectedTotalPages)
    {
        $this->queryBuilder->method('count')->willReturn($resultCount);

        $paginator = $this->createPaginator();
        $paginator->paginate();

        $this->assertEquals($expectedTotalPages, $paginator->getTotalPages());
    }

    /**
     * @return array
     */
    public function getTotalPagesData()
    {
        return [
            [
                static::RESULT_COUNT,
                (int) \ceil(static::RESULT_COUNT / static::PAGE_SIZE),
            ],
            [
                static::RESULT_COUNT_ODD,
                (int) \ceil(static::RESULT_COUNT_ODD / static::PAGE_SIZE),
            ],
        ];
    }

    public function testGetTotalPagesWhenFakePaginate()
    {
        $paginator = $this->testPaginateFake();

        $this->assertEquals(static::CURRENT_PAGE + 1, $paginator->getTotalPages());
    }

    public function testGetTotalPagesWhenFakePaginateHasNoResult()
    {
        $paginator = $this->testPaginateFakeWithoutResult();

        $this->assertEquals(static::CURRENT_PAGE, $paginator->getTotalPages());
    }

    public function testGetTotalPagesWhenNotPaginated()
    {
        $this->expectException(PaginatorException::class);
        $this->expectExceptionMessage('You must call "paginate" or "paginateFake" before calling this method!');

        $this->createPaginator()->getTotalPages();
    }

    public function testGetCurrentPageNumbers()
    {
        $paginator = $this->testPaginate();

        $this->assertEquals(static::CURRENT_PAGE, $paginator->getCurrentPageNumber());
    }

    public function testGetCurrentPageNumbersWhenNotPaginated()
    {
        $this->expectException(PaginatorException::class);
        $this->expectExceptionMessage('You must call "paginate" or "paginateFake" before calling this method!');

        $this->createPaginator()->getCurrentPageNumber();
    }

    public function testGetNextPageNumber()
    {
        $paginator = $this->testPaginate();

        $this->assertEquals(static::CURRENT_PAGE + 1, $paginator->getNextPageNumber());
    }

    public function testGetNextPageNumberWhenPaginateFake()
    {
        $paginator = $this->testPaginateFake();

        $this->assertEquals(static::CURRENT_PAGE + 1, $paginator->getNextPageNumber());
    }

    public function testGetNextPageNumberWhenMaximumPageIsOne()
    {
        $paginator = $this->testPaginateWhenCurrentPageIsHigherThanExistingPage();

        $this->assertEquals(static::NEXT_PAGE_NON_EXISTENT, $paginator->getNextPageNumber());
    }

    public function testGetNextPageNumberWhenNotPaginated()
    {
        $this->expectException(PaginatorException::class);
        $this->expectExceptionMessage('You must call "paginate" or "paginateFake" before calling this method!');

        $this->createPaginator()->getNextPageNumber();
    }

    public function testGetPreviousPageNumber()
    {
        $paginator = $this->testPaginate();

        $this->assertEquals(static::CURRENT_PAGE - 1, $paginator->getPreviousPageNumber());
    }

    public function testGetPreviousPageNumberWhenCurrentPageIsOne()
    {
        $paginator = $this->testPaginateWhenCurrentPageIsHigherThanExistingPage();

        $this->assertEquals(static::PREVIOUS_PAGE_NON_EXISTENT, $paginator->getPreviousPageNumber());
    }

    public function testGetPreviousPageNumberWhenNotPaginated()
    {
        $this->expectException(PaginatorException::class);
        $this->expectExceptionMessage('You must call "paginate" or "paginateFake" before calling this method!');

        $this->createPaginator()->getPreviousPageNumber();
    }

    public function testGetResultTotalCount()
    {
        $paginator = $this->testPaginate();

        $this->assertEquals(static::RESULT_COUNT, $paginator->getResultTotalCount());
    }

    public function testGetResultTotalCountWhenPaginateFakeCalled()
    {
        $paginator = $this->testPaginateFake();

        $this->expectException(PaginatorException::class);
        $this->expectExceptionMessage(
            'When calling "paginateFake", is is not possible to determine result total count!'
        );

        $paginator->getResultTotalCount();
    }

    public function testGetResultTotalCountWhenNotPaginated()
    {
        $this->expectException(PaginatorException::class);
        $this->expectExceptionMessage('You must call "paginate" or "paginateFake" before calling this method!');

        $this->createPaginator()->getResultTotalCount();
    }

    public function testGetPageSize()
    {
        $paginator = $this->testPaginate();

        $this->assertEquals(static::PAGE_SIZE, $paginator->getPageSize());
    }

    public function testGetPageSizeWhenNotPaginated()
    {
        $this->expectException(PaginatorException::class);
        $this->expectExceptionMessage('You must call "paginate" or "paginateFake" before calling this method!');

        $this->createPaginator()->getPageSize();
    }

    /**
     * @return Paginator
     */
    private function createPaginator()
    {
        $this->addPaginateQuery = function (QueryBuilderInterface $queryBuilder, $pageSize, $currentPage) {
            $this
                ->createAccessibleMethod($this->abstractRepository, 'addPaginateQuery')
                ->invoke($this->abstractRepository, $queryBuilder, $pageSize, $currentPage)
            ;
        };
        $this->getPaginateResult = function (QueryBuilderInterface $queryBuilder, $toString) {
            return $this
                ->createAccessibleMethod($this->abstractRepository, 'getPaginateResult')
                ->invoke($this->abstractRepository, $queryBuilder, $toString)
            ;
        };

        return new Paginator(
            $this->queryBuilder,
            $this->addPaginateQuery,
            $this->getPaginateResult,
            static::PAGE_SIZE,
            static::CURRENT_PAGE
        );
    }
}
