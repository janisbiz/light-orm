<?php

namespace Janisbiz\LightOrm\Tests\Behat\Features\Dms\MySQL\Repository;

use Janisbiz\LightOrm\Entity\EntityInterface;
use Janisbiz\LightOrm\Repository\RepositoryInterface;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\FeatureContext;

abstract class AbstractRepositoryFeatureContext extends FeatureContext
{
    /**
     * @var RepositoryInterface
     */
    protected static $repository;

    /**
     * @var \Exception
     */
    protected static $exception;

    /**
     * @var EntityInterface[]
     */
    protected static $entities = [];
}
