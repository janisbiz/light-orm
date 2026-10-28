<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Behat\Features\Dms\MySQL\Repository;

class RepositoryDeleteFeatureContext extends AbstractRepositoryFeatureContext
{
    /**
     * @param string $method
     */
    #[\Behat\Step\When('/^I call method "(.*)" on repository with existing entities$/')]
    public function iCallMethodOnRepositoryWithExistingEntities(string $method)
    {
        $this->callMethodOnRepositoryWithExistingEntities($method);
    }

    /**
     * @param string $method
     */
    #[\Behat\Step\When('/^I call method "(.*)" on repository with existing entities and expecting exception:$/')]
    public function iCallMethodOnRepositoryWithExistingEntitiesAndExpectingException(string $method)
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
    private function callMethodOnRepositoryWithExistingEntities(string $method)
    {
        foreach (static::$entities as $entity) {
            \call_user_func_array(
                [
                    static::$repository,
                    $method,
                ],
                [
                    $entity,
                ]
            );
        }
    }
}
