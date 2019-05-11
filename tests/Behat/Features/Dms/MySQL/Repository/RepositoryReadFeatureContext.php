<?php

namespace Janisbiz\LightOrm\Tests\Behat\Features\Dms\MySQL\Repository;

use Behat\Gherkin\Node\TableNode;
use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Dms\MySQL\Connection\Connection as MySQLConnection;
use Janisbiz\LightOrm\Dms\MySQL\Generator\DmsFactory;
use Janisbiz\LightOrm\Repository\RepositoryInterface;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\FeatureContext;

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
        $returnedRows = static::$repository->$method();

        if (\count($returnedRows) !== \count($rows->getRows()) - 1) {
            throw new \Exception('Count of expected rows doesn\'t match count of returned rows!');
        }

        foreach ($rows as $i => $row) {
            $returnedRow = $returnedRows[$i];

            foreach ($row as $column => $value) {
                $getterMethod = \sprintf('get%s', \ucfirst($column));
                if ($value != $returnedRow->$getterMethod()) {
                    throw new \Exception(\sprintf(
                        'Data mismatch, when reading stored row data! %s::%s => %s != %s => %s',
                        \get_class($returnedRow),
                        $getterMethod,
                        $returnedRow->$getterMethod(),
                        $column,
                        $value
                    ));
                }
            }
        }
    }
}
