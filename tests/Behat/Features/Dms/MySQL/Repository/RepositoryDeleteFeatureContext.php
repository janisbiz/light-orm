<?php

namespace Janisbiz\LightOrm\Tests\Behat\Features\Dms\MySQL\Repository;

class RepositoryDeleteFeatureContext extends AbstractRepositoryFeatureContext
{
    /**
     * @When /^I call method "(.*)" on repository with existing entities$/
     *
     * @param string $method
     */
    public function iCallMethodOnRepositoryWithExistingEntities($method)
    {
        $this->callMethodOnRepositoryWithExistingEntities($method);
    }

    /**
     * @When /^I call method "(.*)" on repository with existing entities and expecting exception:$/
     *
     * @param string $method
     */
    public function iCallMethodOnRepositoryWithExistingEntitiesAndExpectingException($method)
    {
        try {
            $this->callMethodOnRepositoryWithExistingEntities($method);
        } catch (\Exception $e) {
            static::$exception = $e;
        }
    }

    /**
     * @param string $method
     */
    private function callMethodOnRepositoryWithExistingEntities($method)
    {
        foreach (static::$entities as $entity) {
            \call_user_func_array(
                [
                    static::$repository,
                    $method,
                ],
                [
                    'entity' => $entity,
                ]
            );
        }
    }
}
