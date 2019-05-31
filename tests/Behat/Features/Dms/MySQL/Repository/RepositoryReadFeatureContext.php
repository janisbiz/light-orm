<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Behat\Features\Dms\MySQL\Repository;

use Behat\Gherkin\Node\TableNode;

class RepositoryReadFeatureContext extends AbstractRepositoryFeatureContext
{
    /**
     * @Then /^I call method "(.*)" on repository which will return following rows:$/
     *
     * @param string $method
     * @param TableNode $rows
     */
    public function iCallMethodOnRepositoryWhichWillReturnFollowingRows(string $method, TableNode $rows)
    {
        static::$entities = static::$repository->$method();

        $this->iHaveFollowingRows($rows);
    }

    /**
     * @Then /^I have following rows:$/
     *
     * @param TableNode $rows
     *
     * @throws \Exception
     */
    public function iHaveFollowingRows(TableNode $rows)
    {
        if (($entitiesCount = \count(static::$entities)) !== ($expectedRowsCount = \count($rows->getRows()) - 1)) {
            throw new \Exception(\sprintf(
                'Count of expected rows(%d) does not match to count of returned rows(%d)!',
                $expectedRowsCount,
                $entitiesCount
            ));
        }

        foreach ($rows as $i => $row) {
            $entity = static::$entities[$i];

            foreach ($row as $column => $value) {
                $getterMethod = \sprintf('get%s', \ucfirst($column));
                if ($value != $entity->$getterMethod()) {
                    throw new \Exception(\sprintf(
                        'Data mismatch, when reading stored row data! %s::%s => %s != %s => %s',
                        \get_class($entity),
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
     * @Then /^I have following data columns on entities:$/
     *
     * @param TableNode $columns
     *
     * @throws \Exception
     */
    public function iHaveFollowingRowsOfDataColumnsOnEntities(TableNode $columns)
    {
        if (($entitiesCount = \count(static::$entities)) !== ($expectedRowsCount = \count($columns->getRows()) - 1)) {
            throw new \Exception(\sprintf(
                'Count of expected rows(%d) does not match to count of existing entities(%d)!',
                $expectedRowsCount,
                $entitiesCount
            ));
        }

        foreach ($columns as $i => $row) {
            $entity = static::$entities[$i];

            foreach ($row as $column => $value) {
                if ($value != $entity->data($column)) {
                    throw new \Exception(\sprintf(
                        'Data mismatch, when reading stored row data! %s::data(%s) => %s != %s => %s',
                        \get_class($entity),
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
     * @Then /^I call method "(.*)" on repository which will return following integer (\d+)$/
     *
     * @param string $method
     * @param int $integer
     *
     * @throws \Exception
     */
    public function iCallMethodOnRepositoryWhichWillReturnFollowingInteger(string $method, int $integer)
    {
        $returnedInteger = static::$repository->$method();

        if ($returnedInteger !== (int) $integer) {
            throw new \Exception(\sprintf(
                'Expected integer(%d) does not match returned integer(%d)!',
                $integer,
                $returnedInteger
            ));
        }
    }

    /**
     * @codingStandardsIgnoreStart
     * @Then /^I call method "(.*)" on repository which will return paginator with page size of (\d+) and current page (\d+)$/
     * @codingStandardsIgnoreEnd
     *
     * @param string $method
     * @param int $pageSize
     * @param int $currentPage
     */
    public function iCallPaginatorMethodOnRepositoryWithPageSizeOfAndCurrentPage(
        string $method,
        int $pageSize,
        int $currentPage
    ) {
        static::$paginator = static::$repository->$method($pageSize, $currentPage);
    }

    /**
     * @Then /^I call method "(.*)" on paginator which will return entities$/
     *
     * @param string $method
     */
    public function iCallMethodOnPaginatorWhichWillReturnEntities(string $method)
    {
        static::$entities = static::$paginator->$method();
    }

    /**
     * @Then /^I call method "(.*)" on paginator which will return following integer (\d+)$/
     *
     * @param string $method
     * @param int $integer
     *
     * @throws \Exception
     */
    public function iCallMethodOnPaginatorWhichWillReturnFollowingInteger(string $method, int $integer)
    {
        $returnedInteger = static::$paginator->$method();

        if ($returnedInteger !== (int) $integer) {
            throw new \Exception(\sprintf(
                'Expected integer(%d) does not match returned integer(%d)!',
                $integer,
                $returnedInteger
            ));
        }
    }

    /**
     * @Then /^I get following page numbers from paginator:$/
     *
     * @param TableNode $expectedPageNumbers
     */
    public function iGetFollowingPageNumbersFromPaginator(TableNode $expectedPageNumbers)
    {
        $pageNumbers = self::$paginator->getPageNumbers();

        if (($pageNumbersCount = \count($pageNumbers))
            !== ($expectedPageNumbersCount = \count($expectedPageNumbers->getRows()) - 1)
        ) {
            throw new \Exception(\sprintf(
                'Count of expected page numbers(%d) does not match to count of existing page numbers(%d)!',
                $expectedPageNumbersCount,
                $pageNumbersCount
            ));
        }

        foreach ($expectedPageNumbers as $expectedPageNumberRow) {
            foreach ($expectedPageNumberRow as $value) {
                if (!isset($pageNumbers[$value]) || $value != ($pageNumber = $pageNumbers[$value])) {
                    throw new \Exception(\sprintf(
                        'Data mismatch, when reading page numbers! %s != %s',
                        $value,
                        isset($pageNumber) ? $pageNumber : null
                    ));
                }
            }
        }
    }
}
