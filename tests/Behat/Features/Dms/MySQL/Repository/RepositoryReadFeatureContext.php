<?php

namespace Janisbiz\LightOrm\Tests\Behat\Features\Dms\MySQL\Repository;

use Behat\Gherkin\Node\TableNode;

class RepositoryReadFeatureContext extends AbstractRepositoryFeatureContext
{
    /**
     * @Then /^I call method "(.*)" on repository which will return following rows:$/
     *
     * @param string $method
     * @param TableNode $rows
     *
     * @throws \Exception
     */
    public function iCallMethodOnRepositoryWhichWillReturnFollowingRows($method, TableNode $rows)
    {
        static::$entities = static::$repository->$method();

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
    public function iCallMethodOnRepositoryWhichWillReturnFollowingInteger($method, $integer)
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
}
