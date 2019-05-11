<?php

namespace Janisbiz\LightOrm\Tests\Behat\Features\Dms\MySQL\Repository;

use Behat\Gherkin\Node\TableNode;

class RepositoryCreateFeatureContext extends AbstractRepositoryFeatureContext
{
    /**
     * @When /^I call method "(.*)" on repository with parameters:$/
     *
     * @param string $method
     * @param TableNode $parameters
     */
    public function iCallMethodOnRepositoryWithParameters($method, TableNode $parameters)
    {
        try {
            foreach ($parameters as $methodParameters) {
                \call_user_func_array([static::$repository, $method], $methodParameters);
            }
        } catch (\Exception $e) {
            static::$exception = $e;
        }
    }
}
