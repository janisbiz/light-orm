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
     * @Then /^I call method "(.*)" with parameters:$/
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
}