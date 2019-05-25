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
     * @param null|double|int|string $value
     *
     * @return null
     * @throws RepositoryException
     */
    public function quote($value)
    {
        switch ($phpParamType = \mb_strtolower(\gettype($value))) {
            case 'null':
                $pdoParamType = \PDO::PARAM_NULL;

                break;

            case 'integer':
                $pdoParamType = \PDO::PARAM_INT;

                break;

            case 'double':
            case 'string':
                $pdoParamType = \PDO::PARAM_STR;

                break;

            case 'boolean':
                $pdoParamType = \PDO::PARAM_BOOL;

                break;

            default:
                throw new RepositoryException(\sprintf(
                    'Parameter type "%s" could not be quoted for SQL execution!',
                    $phpParamType
                ));
        }

        return $this->getConnection()->quote($value, $pdoParamType);
    }

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
