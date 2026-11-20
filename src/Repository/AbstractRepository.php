<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Repository;

use Closure;
use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Entity\EntityInterface;
use Janisbiz\LightOrm\ConnectionPool;
use Janisbiz\LightOrm\Generator\Writer\WriterInterface;
use Janisbiz\LightOrm\Paginator\Paginator;
use Janisbiz\LightOrm\Paginator\PaginatorInterface;
use Janisbiz\LightOrm\QueryBuilder\QueryBuilderInterface;
use PDOStatement;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LogLevel;

abstract class AbstractRepository implements RepositoryInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected ?ConnectionPool $connectionPool = null;

    protected ?bool $commit = null;

    protected function paginator(
        QueryBuilderInterface $queryBuilder,
        int $pageSize,
        int $currentPage = 1,
        array $options = []
    ): PaginatorInterface {
        if ($pageSize < 1) {
            $pageSize = 1;
        }

        if ($currentPage < 1) {
            $currentPage = 1;
        }

        return new Paginator(
            $queryBuilder,
            function (QueryBuilderInterface $queryBuilder, $pageSize, $currentPage): void {
                $this->addPaginateQuery($queryBuilder, $pageSize, $currentPage);
            },
            fn(QueryBuilderInterface $queryBuilder, $toString) => $this->getPaginateResult($queryBuilder, $toString),
            $pageSize,
            $currentPage,
            $options
        );
    }

    protected function log(string $level, string $message, array $context = []): static
    {
        if (null === $this->logger) {
            return $this;
        }

        $this->logger->log($level, $message, $context);

        return $this;
    }

    protected function beginTransaction(?ConnectionInterface $connection): static
    {
        $connection ??= $this->getConnection();

        if (true === ($this->commit = !$connection->inTransaction())) {
            $connection->beginTransaction();
        }

        return $this;
    }

    protected function commit(?ConnectionInterface $connection): bool
    {
        $connection ??= $this->getConnection();

        if (true === $this->commit) {
            return $connection->commit();
        }

        return true;
    }

    protected function rollback(?ConnectionInterface $connection = null): static
    {
        $connection ??= $this->getConnection();

        if ($connection->inTransaction()) {
            $connection->rollBack();
        }

        return $this;
    }

    protected function prepareAndExecute(
        QueryBuilderInterface $queryBuilder,
        array $bindData,
        ?ConnectionInterface $connection = null
    ): PDOStatement {
        $connection ??= $this->getConnection();

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

    protected function getConnection(): ConnectionInterface
    {
        if (null === $this->connectionPool) {
            $this->connectionPool = new ConnectionPool();
        }

        return $this->connectionPool->getConnection(
            $this->getEntityClassConstant(WriterInterface::CLASS_CONSTANT_DATABASE_NAME)
        );
    }

    protected function getEntityClassConstant(string $constant): string
    {
        return constant(sprintf('%s::%s', $this->getEntityClass(), $constant));
    }

    protected function createRepositoryCallClosure(): Closure
    {
        return fn($methodName, QueryBuilderInterface $queryBuilder, $toString) =>
            $this->$methodName($queryBuilder, $toString);
    }

    abstract protected function createQueryBuilder(?EntityInterface $entity): QueryBuilderInterface;

    abstract protected function getEntityClass(): string;

    abstract protected function addPaginateQuery(
        QueryBuilderInterface $queryBuilder,
        int $pageSize,
        int $currentPage
    ): static;

    /**
     * @return EntityInterface[]
     */
    abstract protected function getPaginateResult(QueryBuilderInterface $queryBuilder, bool $toString = false): array;
}
