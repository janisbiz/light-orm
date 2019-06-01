<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Repository;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Entity\EntityInterface;
use Janisbiz\LightOrm\ConnectionPool;
use Janisbiz\LightOrm\Generator\Writer\WriterInterface;
use Janisbiz\LightOrm\Paginator\Paginator;
use Janisbiz\LightOrm\Paginator\PaginatorInterface;
use Janisbiz\LightOrm\QueryBuilder\QueryBuilderInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LogLevel;

abstract class AbstractRepository implements RepositoryInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

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
     * @return string
     * @throws RepositoryException
     */
    protected function quote($value)
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
     * @param QueryBuilderInterface $queryBuilder
     * @param int $pageSize
     * @param int $currentPage
     * @param array $options
     *
     * @return PaginatorInterface
     */
    protected function paginator(
        QueryBuilderInterface $queryBuilder,
        int $pageSize,
        int $currentPage = 1,
        array $options = []
    ): PaginatorInterface {
        if ($pageSize < 1) {
            $pageSize = 1;
        }

        return new Paginator(
            $queryBuilder,
            function (QueryBuilderInterface $queryBuilder, $pageSize, $currentPage) {
                $this->addPaginateQuery($queryBuilder, $pageSize, $currentPage);
            },
            function (QueryBuilderInterface $queryBuilder, $toString) {
                return $this->getPaginateResult($queryBuilder, $toString);
            },
            $pageSize,
            $currentPage,
            $options
        );
    }

    /**
     * @param string $level
     * @param string $message
     * @param array $context
     *
     * @return $this
     */
    protected function log(string $level, string $message, array $context = []): AbstractRepository
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
    protected function beginTransaction(ConnectionInterface $connection = null): AbstractRepository
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
     * @return bool
     */
    protected function commit(ConnectionInterface $connection = null): bool
    {
        if (null === $connection) {
            $connection = $this->getConnection();
        }

        if (true === $this->commit) {
            return $connection->commit();
        }

        return true;
    }

    /**
     * @param ConnectionInterface|null $connection
     *
     * @return $this
     */
    protected function rollback(ConnectionInterface $connection = null): AbstractRepository
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
     * @return \PDOStatement
     */
    protected function prepareAndExecute(
        QueryBuilderInterface $queryBuilder,
        array $bindData,
        ConnectionInterface $connection = null
    ): \PDOStatement {
        if (null === $connection) {
            $connection = $this->getConnection();
        }

        $statement = $connection->prepare($queryBuilder->buildQuery());
        $statement->execute($bindData);

        $this->log(
            LogLevel::DEBUG,
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
    protected function getConnection(): ConnectionInterface
    {
        if (null === $this->connectionPool) {
            $this->connectionPool = new ConnectionPool();
        }

        return $this->connectionPool->getConnection(
            $this->getEntityClassConstant(WriterInterface::CLASS_CONSTANT_DATABASE_NAME)
        );
    }

    /**
     * @param string $constant
     *
     * @return string
     */
    protected function getEntityClassConstant($constant): string
    {
        return \constant(\sprintf('%s::%s', $this->getEntityClass(), $constant));
    }

    /**
     * @return \Closure
     */
    protected function createRepositoryCallClosure(): \Closure
    {
        return function ($methodName, QueryBuilderInterface $queryBuilder, $toString) {
            return $this->$methodName($queryBuilder, $toString);
        };
    }

    /**
     * @param EntityInterface|null $entity
     *
     * @return QueryBuilderInterface
     */
    abstract protected function createQueryBuilder(EntityInterface $entity = null): QueryBuilderInterface;

    /**
     * @return string
     */
    abstract protected function getEntityClass(): string;

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param int $pageSize
     * @param int $currentPage
     *
     * @return $this
     */
    abstract protected function addPaginateQuery(
        QueryBuilderInterface $queryBuilder,
        int $pageSize,
        int $currentPage
    ): AbstractRepository;

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param bool $toString
     *
     * @return EntityInterface[]
     */
    abstract protected function getPaginateResult(QueryBuilderInterface $queryBuilder, bool $toString = false): array;
}
