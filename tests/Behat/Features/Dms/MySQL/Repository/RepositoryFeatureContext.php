<?php

namespace Janisbiz\LightOrm\Tests\Behat\Features\Dms\MySQL\Repository;

use Behat\Gherkin\Node\TableNode;
use Janisbiz\LightOrm\Repository\RepositoryInterface;
use Janisbiz\LightOrm\Tests\Behat\Features\Connection\ConnectionFeatureContext;

class RepositoryFeatureContext extends ConnectionFeatureContext
{
    private $repository;

    /**
     * @Given /^I create repository "(.*)"$/
     *
     * @param $repositoryClass
     *
     * @throws \Exception
     */
    public function iCreateRepository($repositoryClass)
    {
        $this->repository = new $repositoryClass;

        if (!$this->repository instanceof RepositoryInterface) {
            throw new \Exception(\sprintf(
                'Class "%s" must implement "%s"',
                \get_class($this->repository),
                RepositoryInterface::class
            ));
        }
    }

    /**
     * @When /^I call method "(.*)" with parameters:$/
     *
     * @param string $method
     * @param TableNode $parameters
     */
    public function iCallMethodOnRepositoryWithParameters($method, TableNode $parameters)
    {
        foreach ($parameters as $methodParameters) {
            \call_user_func_array([$this->repository, $method], $methodParameters);
        }
    }

    /**
     * @Then /^I call method "(.*)" which will return of following rows:$/
     *
     * @param string $method
     * @param TableNode $rows
     *
     * @throws \Exception
     */
    public function iCallMethodWhichWillReturnOfFollowingRows($method, TableNode $rows)
    {
        $returnedRows = $this->repository->$method();

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
