<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Behat\Features\Dms\MySQL\Repository;

use Behat\Gherkin\Node\TableNode;

class RepositoryUpdateFeatureContext extends AbstractRepositoryFeatureContext
{
    /**
     * @When /^I call method "(.*)" on repository with existing entities and parameters:$/
     *
     * @param string $method
     * @param TableNode $parameters
     */
    public function iCallMethodOnRepositoryWithExistingEntitiesAndParameters(string $method, TableNode $parameters)
    {
        $this->callMethodOnRepositoryWithExistingEntitiesAndParameters($method, $parameters);
    }
    /**
     * @When /^I call method "(.*)" on repository with existing entities and parameters and expecting exception:$/
     *
     * @param string $method
     * @param TableNode $parameters
     */
    public function iCallMethodOnRepositoryWithExistingEntitiesAndParametersAndExpectingException(
        string $method,
        TableNode $parameters
    ) {
        try {
            $this->callMethodOnRepositoryWithExistingEntitiesAndParameters($method, $parameters);
        } catch (\Exception $e) {
            static::$entities = [];
            static::$exception = $e;
        }
    }

    /**
     * @param string $method
     * @param TableNode $parameters
     */
    private function callMethodOnRepositoryWithExistingEntitiesAndParameters(string $method, TableNode $parameters)
    {
        $entitiesStorage = static::$entities;
        static::$entities = [];

        foreach ($parameters as $i => $methodParameters) {
            static::$entities[] = \call_user_func_array(
                [
                    static::$repository,
                    $method,
                ],
                [
                    'entity' => $entitiesStorage[$i],
                ]
                + $methodParameters
            );
        }

        \array_filter(static::$entities);
    }
}
