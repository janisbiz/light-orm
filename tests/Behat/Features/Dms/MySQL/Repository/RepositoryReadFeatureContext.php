<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Behat\Features\Dms\MySQL\Repository;

use Behat\Gherkin\Node\TableNode;
use Exception;

class RepositoryReadFeatureContext extends AbstractRepositoryFeatureContext
{
    #[\Behat\Step\Then('/^I call method "(.*)" on repository which will return following rows:$/')]
    public function iCallMethodOnRepositoryWhichWillReturnFollowingRows(string $method, TableNode $rows)
    {
        static::$entities = static::$repository->$method();

        $this->iHaveFollowingRows($rows);
    }

    /**
     * @throws Exception
     */
    #[\Behat\Step\Then('/^I have following rows:$/')]
    public function iHaveFollowingRows(TableNode $rows)
    {
        if (($entitiesCount = count(static::$entities)) !== ($expectedRowsCount = count($rows->getRows()) - 1)) {
            throw new Exception(sprintf(
                'Count of expected rows(%d) does not match to count of returned rows(%d)!',
                $expectedRowsCount,
                $entitiesCount
            ));
        }

        foreach ($this->normalizeTableNode($rows) as $i => $row) {
            $entity = static::$entities[$i];

            foreach ($row as $column => $value) {
                $getterMethod = sprintf('get%s', ucfirst($column));
                if ($value !== $entity->$getterMethod()) {
                    throw new Exception(sprintf(
                        'Data mismatch, when reading stored row data! %s::%s => %s != %s => %s',
                        $entity::class,
                        $getterMethod,
                        $entity->$getterMethod(),
                        $column,
                        $value
                    ));
                }
            }
        }
    }

    /**
     * @throws Exception
     */
    #[\Behat\Step\Then('/^I have following data columns on entities:$/')]
    public function iHaveFollowingRowsOfDataColumnsOnEntities(TableNode $columns)
    {
        if (($entitiesCount = count(static::$entities)) !== ($expectedRowsCount = count($columns->getRows()) - 1)) {
            throw new Exception(sprintf(
                'Count of expected rows(%d) does not match to count of existing entities(%d)!',
                $expectedRowsCount,
                $entitiesCount
            ));
        }

        foreach ($this->normalizeTableNode($columns) as $i => $row) {
            $entity = static::$entities[$i];

            foreach ($row as $column => $value) {
                if ($value !== $entity->data($column)) {
                    throw new Exception(sprintf(
                        'Data mismatch, when reading stored row data! %s::data(%s) => %s != %s => %s',
                        $entity::class,
                        $column,
                        $entity->data($column),
                        $column,
                        $value
                    ));
                }
            }
        }
    }

    /**
     * @throws Exception
     */
    #[\Behat\Step\Then('/^I call method "(.*)" on repository which will return following integer (\d+)$/')]
    public function iCallMethodOnRepositoryWhichWillReturnFollowingInteger(string $method, int $integer)
    {
        $returnedInteger = static::$repository->$method();

        if ($returnedInteger !== (int) $integer) {
            throw new Exception(sprintf(
                'Expected integer(%d) does not match returned integer(%d)!',
                $integer,
                $returnedInteger
            ));
        }
    }

    #[\Behat\Step\Then('/^I call method "(.*)" on repository which will return paginator with page size of (\d+) and current page (\d+)$/')] // phpcs:ignore
    public function iCallPaginatorMethodOnRepositoryWithPageSizeOfAndCurrentPage(
        string $method,
        int $pageSize,
        int $currentPage
    ) {
        static::$paginator = static::$repository->$method($pageSize, $currentPage);
    }

    #[\Behat\Step\Then('/^I call method "(.*)" on paginator which will return entities$/')]
    public function iCallMethodOnPaginatorWhichWillReturnEntities(string $method)
    {
        static::$entities = static::$paginator->$method();
    }

    /**
     * @throws Exception
     */
    #[\Behat\Step\Then('/^I call method "(.*)" on paginator which will return following integer (\d+)$/')]
    public function iCallMethodOnPaginatorWhichWillReturnFollowingInteger(string $method, int $integer)
    {
        $returnedInteger = static::$paginator->$method();

        if ($returnedInteger !== (int) $integer) {
            throw new Exception(sprintf(
                'Expected integer(%d) does not match returned integer(%d)!',
                $integer,
                $returnedInteger
            ));
        }
    }

    /**
     * @throws Exception
     */
    #[\Behat\Step\Then('/^I get following page numbers from paginator:$/')]
    public function iGetFollowingPageNumbersFromPaginator(TableNode $expectedPageNumbers)
    {
        $pageNumbers = self::$paginator->getPageNumbers();

        if (($pageNumbersCount = count($pageNumbers))
            !== ($expectedPageNumbersCount = count($expectedPageNumbers->getRows()) - 1)
        ) {
            throw new Exception(sprintf(
                'Count of expected page numbers(%d) does not match to count of existing page numbers(%d)!',
                $expectedPageNumbersCount,
                $pageNumbersCount
            ));
        }

        foreach ($this->normalizeTableNode($expectedPageNumbers) as $expectedPageNumberRow) {
            foreach ($expectedPageNumberRow as $value) {
                if (!isset($pageNumbers[$value]) || $value !== ($pageNumber = $pageNumbers[$value])) {
                    throw new Exception(sprintf(
                        'Data mismatch, when reading page numbers! %s != %s',
                        $value,
                        $pageNumber ?? null
                    ));
                }
            }
        }
    }
}
