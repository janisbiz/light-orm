<?php

namespace Janisbiz\LightOrm\Repository;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Entity\EntityInterface;
use Janisbiz\LightOrm\ConnectionPool;
use Janisbiz\LightOrm\Generator\Writer\WriterInterface;
use Janisbiz\LightOrm\QueryBuilder\QueryBuilderInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerTrait;

abstract class AbstractRepository implements RepositoryInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    use LoggerTrait;

    /**
     * @var ConnectionPool
     */
    protected $connectionPool;

    /**
     * @var null|bool
     */
    protected $commit;

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
     * @param string $level
     * @param string $message
     * @param array $context
     *
     * @return $this
     */
    public function log($level, $message, array $context = [])
    {
        if (null === $this->logger) {
            return $this;
        }

        $this->logger->log($level, $message, $context);

        return $this;
    }

    /**
     * @param ConnectionInterface|null $connection
     *
     * @return $this
     */
    protected function beginTransaction(ConnectionInterface $connection = null)
    {
        if (null === $connection) {
            $connection = $this->getConnection();
        }

        if (true === ($this->commit = !$connection->inTransaction())) {
            $connection->beginTransaction();
        }

        return $this;
    }

    /**
     * @param ConnectionInterface|null $connection
     *
     * @return $this
     */
    protected function commit(ConnectionInterface $connection = null)
    {
        if (null === $connection) {
            $connection = $this->getConnection();
        }

        if (true === $this->commit) {
            $connection->commit();
        }

        return $this;
    }

    /**
     * @param ConnectionInterface|null $connection
     *
     * @return $this
     */
    protected function rollback(ConnectionInterface $connection = null)
    {
        if (null === $connection) {
            $connection = $this->getConnection();
        }

        if ($connection->inTransaction()) {
            $connection->rollBack();
        }

        return $this;
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param array $bindData
     * @param ConnectionInterface|null $connection
     *
     * @return bool|\PDOStatement
     */
    protected function prepareAndExecute(
        QueryBuilderInterface $queryBuilder,
        array $bindData,
        ConnectionInterface $connection = null
    ) {
        if (null === $connection) {
            $connection = $this->getConnection();
        }

        $statement = $connection->prepare($queryBuilder->buildQuery());
        $statement->execute($bindData);

        $this->debug(
            'Execute query "{query}" with parameters "{parameters}".',
            [
                'query' => $queryBuilder->buildQuery(),
                'parameters' => $bindData,
            ]
        );

        return $statement;
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
