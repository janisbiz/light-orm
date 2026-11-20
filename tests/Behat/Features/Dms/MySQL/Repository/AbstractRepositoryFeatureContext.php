<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Behat\Features\Dms\MySQL\Repository;

use Janisbiz\LightOrm\Entity\EntityInterface;
use Janisbiz\LightOrm\Paginator\PaginatorInterface;
use Janisbiz\LightOrm\Repository\RepositoryInterface;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\AbstractFeatureContext;
use Throwable;

abstract class AbstractRepositoryFeatureContext extends AbstractFeatureContext
{
    /**
     * @var RepositoryInterface[]
     */
    protected static array $repositories = [];

    protected static RepositoryInterface $repository;

    protected static Throwable $exception;

    /**
     * @var EntityInterface[]
     */
    protected static array $entities = [];

    protected static PaginatorInterface $paginator;
}
