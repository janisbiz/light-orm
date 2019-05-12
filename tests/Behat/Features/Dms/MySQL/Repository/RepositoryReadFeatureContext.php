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
}
