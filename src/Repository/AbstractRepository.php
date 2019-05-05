<?php

namespace Janisbiz\LightOrm\Repository;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Entity\EntityInterface;
use Janisbiz\LightOrm\ConnectionPool;
use Janisbiz\LightOrm\Generator\Writer\WriterInterface;
use Janisbiz\LightOrm\QueryBuilder\QueryBuilderInterface;

abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @var ConnectionPool
     */
    protected $connectionPool;

    /**
     * @return ConnectionInterface
     */
    protected function getConnection()
    {
        if (null === $this->connectionPool) {
            $this->connectionPool = new ConnectionPool();
        }

        return $this->connectionPool->getConnection(
            $this->getModelClassConstant(WriterInterface::CLASS_CONSTANT_DATABASE_NAME)
        );
    }

    /**
     * @param string $constant
     *
     * @return string|int
     */
    protected function getModelClassConstant($constant)
    {
        return \constant(\sprintf('%s::%s', $this->getModelClass(), $constant));
    }

    /**
     * @param EntityInterface|null $entity
     *
     * @return QueryBuilderInterface
     */
    abstract protected function createQueryBuilder(EntityInterface $entity = null);

    /**
     * @return string
     */
    abstract protected function getModelClass();
}
