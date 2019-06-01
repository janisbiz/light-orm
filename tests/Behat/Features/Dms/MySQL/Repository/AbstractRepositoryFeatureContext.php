<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Behat\Features\Dms\MySQL\Repository;

use Janisbiz\LightOrm\Entity\EntityInterface;
use Janisbiz\LightOrm\Paginator\PaginatorInterface;
use Janisbiz\LightOrm\Repository\RepositoryInterface;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\AbstractFeatureContext;

abstract class AbstractRepositoryFeatureContext extends AbstractFeatureContext
{
    /**
     * @var RepositoryInterface[]
     */
    protected static $repositories = [];

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

    /**
     * @var PaginatorInterface
     */
    protected static $paginator;
}
