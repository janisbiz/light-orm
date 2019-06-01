<?php declare(strict_types=1);

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
    public function iCallMethodOnRepositoryWithParameters(string $method, TableNode $parameters)
    {
        $this->callMethodOnRepositoryWithParameters($method, $parameters);
    }
    /**
     * @When /^I call method "(.*)" on repository with parameters and expecting exception:$/
     *
     * @param string $method
     * @param TableNode $parameters
     */
    public function iCallMethodOnRepositoryWithParametersAndExpectingException(string $method, TableNode $parameters)
    {
        try {
            $this->callMethodOnRepositoryWithParameters($method, $parameters);
        } catch (\Exception $e) {
            static::$entities = [];

            static::$exception = $e;
        }
    }

    /**
     * @Then I have following entities with identifiers set:
     *
     * @param TableNode $identifiers
     *
     * @throws \Exception
     */
    public function iHaveFollowingEntitiesWithIdentifiersSet(TableNode $identifiers)
    {
        if (($entitiesCount = \count(static::$entities))
            !== ($identifiersCount = \count($identifiers->getRows()) - 1)
        ) {
            throw new \Exception(\sprintf(
                'Count of entities(%d) does not match to count of identifiers(%d)!',
                $entitiesCount,
                $identifiersCount
            ));
        }

        foreach ($this->normalizeTableNode($identifiers) as $i => $identifier) {
            $entity = static::$entities[$i];

            foreach ($identifier as $identifierName => $identifierValue) {
                $getterMethod = \sprintf('get%s', \ucfirst($identifierName));
                if ($identifierValue != $entity->$getterMethod()) {
                    throw new \Exception(\sprintf(
                        'Identifiers does not match! %s::%s => %s != %s => %s',
                        \get_class($entity),
                        $getterMethod,
                        $entity->$getterMethod(),
                        $identifierName,
                        $identifierValue
                    ));
                }
            }
        }
    }

    /**
     * @param string $method
     * @param TableNode $parameters
     */
    private function callMethodOnRepositoryWithParameters(string $method, TableNode $parameters)
    {
        static::$entities = [];

        foreach ($this->normalizeTableNode($parameters) as $methodParameters) {
            $methodResult = \call_user_func_array([static::$repository, $method], $methodParameters);

            if (\is_array($methodResult)) {
                static::$entities = \array_merge(static::$entities, $methodResult);
            } else {
                static::$entities[] = $methodResult;
            }
        }

        \array_filter(static::$entities);
    }
}
