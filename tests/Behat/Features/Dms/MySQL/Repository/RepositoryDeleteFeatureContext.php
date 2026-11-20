<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Behat\Features\Dms\MySQL\Repository;

use Exception;

class RepositoryDeleteFeatureContext extends AbstractRepositoryFeatureContext
{
    #[\Behat\Step\When('/^I call method "(.*)" on repository with existing entities$/')]
    public function iCallMethodOnRepositoryWithExistingEntities(string $method)
    {
        $this->callMethodOnRepositoryWithExistingEntities($method);
    }

    #[\Behat\Step\When('/^I call method "(.*)" on repository with existing entities and expecting exception:$/')]
    public function iCallMethodOnRepositoryWithExistingEntitiesAndExpectingException(string $method)
    {
        try {
            $this->callMethodOnRepositoryWithExistingEntities($method);
        } catch (Exception $e) {
            static::$exception = $e;
        }
    }

    private function callMethodOnRepositoryWithExistingEntities(string $method)
    {
        foreach (static::$entities as $entity) {
            call_user_func_array(
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
