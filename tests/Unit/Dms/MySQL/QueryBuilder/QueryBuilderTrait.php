<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilder;
use Janisbiz\LightOrm\Entity\EntityInterface;
use Janisbiz\LightOrm\Repository\RepositoryInterface;

trait QueryBuilderTrait
{
    /**
     * @param RepositoryInterface $repository
     * @param EntityInterface|null $entity
     *
     * @return QueryBuilder
     */
    protected function createQueryBuilder(RepositoryInterface $repository, EntityInterface $entity = null)
    {
        $abstractRepositoryCreateClosureMethod = new \ReflectionMethod($repository, 'createRepositoryCallClosure');
        $abstractRepositoryCreateClosureMethod->setAccessible(true);

        return new QueryBuilder($abstractRepositoryCreateClosureMethod->invoke($repository), $entity);
    }
}
