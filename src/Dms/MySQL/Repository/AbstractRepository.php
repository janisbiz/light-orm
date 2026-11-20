<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\Repository;

use Janisbiz\LightOrm\Dms\MySQL\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Dms\MySQL\Enum\CommandEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilder;
use Janisbiz\LightOrm\Entity\EntityInterface;
use Janisbiz\LightOrm\Generator\Writer\WriterInterface;
use Janisbiz\LightOrm\QueryBuilder\QueryBuilderInterface as BaseQueryBuilderInterface;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderInterface;
use Janisbiz\LightOrm\Repository\AbstractRepository as BaseAbstractRepository;
use PDO;
use Throwable;

/**
 * @method ConnectionInterface getConnection()
 */
abstract class AbstractRepository extends BaseAbstractRepository
{
    /**
     * @throws RepositoryException|Throwable
     */
    protected function insert(QueryBuilderInterface $queryBuilder, bool $toString = false): string|EntityInterface
    {
        if (!($entity = $queryBuilder->getEntity())) {
            throw new RepositoryException(
                'Cannot perform insert on query without entity! Please create query builder with entity.'
            );
        }

        $this->addEntityInsertQuery($queryBuilder, $entity);

        if (true === $toString) {
            return $queryBuilder->toString();
        }


        $this->beginTransaction($connection = $this->getConnection());

        try {
            $this->prepareAndExecute($queryBuilder, $queryBuilder->bindValueData(), $connection);
        } catch (Throwable $e) {
            $this->rollBack($connection);

            throw $e;
        }

        if (!empty($primaryKeysAutoIncrement = $entity->primaryKeysAutoIncrement())) {
            $entityData = &$entity->data();

            foreach ($primaryKeysAutoIncrement as $primaryKeyAutoIncrement) {
                $entityData[$primaryKeyAutoIncrement] = (int) $connection->lastInsertId($entity::TABLE_NAME);
            }
        }

        $this->commit($connection);

        return $entity;
    }

    protected function insertIgnore(QueryBuilderInterface $queryBuilder, bool $toString = false): string|EntityInterface
    {
        return $this->insert($queryBuilder, $toString);
    }

    protected function replace(QueryBuilderInterface $queryBuilder, bool $toString = false): string|EntityInterface
    {
        return $this->insert($queryBuilder, $toString);
    }

    /**
     * @throws Throwable
     */
    protected function findOne(QueryBuilderInterface $queryBuilder, bool $toString = false): string|EntityInterface|null
    {
        if (true === $toString) {
            return $queryBuilder->toString();
        }

        try {
            $statement = $this->prepareAndExecute($queryBuilder, $queryBuilder->bindData());
            $statement->setFetchMode(
                PDO::FETCH_CLASS,
                ($entity = $queryBuilder->getEntity()) ? $entity::class : $this->getEntityClass(),
                [
                    false,
                ]
            );
        } catch (Throwable $e) {
            $this->rollback();

            throw $e;
        }

        return $statement->fetch() ?: null;
    }

    /**
     * @throws Throwable
     */
    protected function find(QueryBuilderInterface $queryBuilder, bool $toString = false): array|string
    {
        if (true === $toString) {
            return $queryBuilder->toString();
        }

        try {
            $statement = $this->prepareAndExecute($queryBuilder, $queryBuilder->bindData(), null);
            $statement->setFetchMode(
                PDO::FETCH_CLASS,
                ($entity = $queryBuilder->getEntity()) ? $entity::class : $this->getEntityClass(),
                [
                    false,
                ]
            );
        } catch (Throwable $e) {
            $this->rollback(null);

            throw $e;
        }

        return $statement->fetchAll() ?: [];
    }

    /**
     * @throws Throwable
     */
    protected function update(QueryBuilderInterface $queryBuilder, bool $toString = false): string|EntityInterface|bool
    {
        if (($entity = $queryBuilder->getEntity()) && !$this->addEntityUpdateQuery($queryBuilder, $entity, $toString)) {
            return $entity;
        }

        if (true === $toString) {
            return $queryBuilder->toString();
        }

        $this->beginTransaction($connection = $this->getConnection());

        try {
            $connection->setSqlSafeUpdates();

            $this->prepareAndExecute($queryBuilder, $queryBuilder->bindData(), $connection);

            $connection->unsetSqlSafeUpdates();
        } catch (Throwable $e) {
            $this->rollBack($connection);
            $connection->unsetSqlSafeUpdates();

            throw $e;
        }

        return $this->commit($connection) ? ($entity ?: true) : false;
    }

    protected function updateIgnore(QueryBuilderInterface $queryBuilder, bool $toString = false): string|EntityInterface
    {
        return $this->update($queryBuilder, $toString);
    }

    /**
     * @throws Throwable
     */
    protected function delete(QueryBuilderInterface $queryBuilder, bool $toString = false): string|bool
    {
        if (($entity = $queryBuilder->getEntity()) && !$this->addEntityDeleteQuery($queryBuilder, $entity)) {
            return false;
        }

        if (true === $toString) {
            return $queryBuilder->toString();
        }

        $this->beginTransaction($connection = $this->getConnection());

        try {
            $connection->setSqlSafeUpdates();

            $this->prepareAndExecute($queryBuilder, $queryBuilder->bindData(), $connection);

            $connection->unsetSqlSafeUpdates();
        } catch (Throwable $e) {
            $this->rollBack($connection);
            $connection->unsetSqlSafeUpdates();

            throw $e;
        }

        return $this->commit($connection);
    }

    /**
     * @throws RepositoryException|Throwable
     */
    protected function count(QueryBuilderInterface $queryBuilder, bool $toString = false): string|int
    {
        if (CommandEnum::SELECT !== $queryBuilder->commandData()) {
            throw new RepositoryException(sprintf(
                'Command "%s" is not a valid command for count query! Use "%s" command to execute count query.',
                $queryBuilder->commandData(),
                CommandEnum::SELECT
            ));
        }

        $queryBuilderCount = $this
            ->createQueryBuilder()
            ->command($queryBuilder->commandData())
            ->column('COUNT(*)', true)
            ->table(\sprintf('(%s) AS total_count', $queryBuilder->buildQuery()), true)
            ->bind($queryBuilder->bindData())
        ;

        if ($toString === true) {
            return $queryBuilderCount->toString();
        }

        $connection = $this->getConnection();

        try {
            $statement = $this->prepareAndExecute($queryBuilderCount, $queryBuilderCount->bindData(), $connection);
        } catch (Throwable $e) {
            $this->rollBack($connection);

            throw $e;
        }

        return $statement->fetchColumn(0);
    }

    protected function createQueryBuilder(?EntityInterface $entity = null): QueryBuilderInterface
    {
        return (new QueryBuilder($this->createRepositoryCallClosure(), $entity))
            ->column(sprintf('%s.*', $this->getEntityClassConstant(WriterInterface::CLASS_CONSTANT_TABLE_NAME)))
            ->table($this->getEntityClassConstant(WriterInterface::CLASS_CONSTANT_TABLE_NAME))
        ;
    }

    protected function addEntityInsertQuery(QueryBuilderInterface $queryBuilder, EntityInterface $entity): static
    {
        $entityColumns = $entity->columns();
        $entityData = &$entity->data();

        foreach ($entityColumns as $column) {
            if (array_key_exists($column, $entityData)) {
                $queryBuilder->value($column, $entityData[$column]);
            }
        }

        return $this;
    }

    protected function addEntityUpdateQuery(
        QueryBuilderInterface $queryBuilder,
        EntityInterface $entity,
        bool $toString
    ): string|bool {
        $performUpdate = false;
        $entityData = &$entity->data();
        $entityDataOriginal = &$entity->dataOriginal();

        if (false === $entity->isNew() || true === $entity->isSaved()) {
            foreach ($entity->primaryKeys() as $primaryKey) {
                if (isset($entityDataOriginal[$primaryKey])) {
                    $queryBuilder->where(
                        sprintf('%s.%s = :%s_WhereUpdate', $entity::TABLE_NAME, $primaryKey, $primaryKey),
                        [
                            sprintf('%s_WhereUpdate', $primaryKey) => $entityDataOriginal[$primaryKey],
                        ]
                    );
                }
            }
        } else {
            return $performUpdate;
        }

        foreach ($entity->columns() as $column) {
            /** Updating only what is needed to update, for faster queries, skipping equal values */
            if (!array_key_exists($column, $entityDataOriginal)
                || (isset($entityDataOriginal[$column]) && $entityDataOriginal[$column] == $entityData[$column])
            ) {
                continue;
            } elseif (false === $performUpdate) {
                $performUpdate = true;
            }

            $queryBuilder->set(sprintf('%s.%s', $entity::TABLE_NAME, $column), $entityData[$column]);

            /**
             * If we want to get query as astring, we don't perform actions on original data as update won't be
             * performed!
             */
            if (false === $toString) {
                $entityDataOriginal[$column] = $entityData[$column];
            }
        }

        return $performUpdate;
    }

    protected function addEntityDeleteQuery(QueryBuilderInterface $queryBuilder, EntityInterface $entity): bool
    {
        $performDelete = false;
        $entityData = $entity->data();

        foreach ($entity->primaryKeys() as $primaryKey) {
            if (isset($entityData[$primaryKey]) && !is_null($entityData[$primaryKey])) {
                if (false === $performDelete) {
                    $performDelete = true;
                }

                $primaryKeyNormalised = sprintf(
                    '%s_Delete',
                    implode(
                        '',
                        array_map(
                            fn($columnPart) => mb_convert_case($columnPart, MB_CASE_TITLE),
                            array_merge(
                                explode('.', $entity::TABLE_NAME),
                                [
                                    $primaryKey,
                                ]
                            )
                        )
                    )
                );

                $queryBuilder->where(
                    sprintf('%s.%s = :%s', $entity::TABLE_NAME, $primaryKey, $primaryKeyNormalised),
                    [
                        $primaryKeyNormalised => $entityData[$primaryKey],
                    ]
                );
            }
        }

        return $performDelete;
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     */
    protected function addPaginateQuery(
        BaseQueryBuilderInterface $queryBuilder,
        int $pageSize,
        int $currentPage
    ): static {
        $queryBuilder->limitWithOffset($pageSize, $pageSize * $currentPage - $pageSize);

        return $this;
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     *
     * @return EntityInterface[]
     */
    protected function getPaginateResult(BaseQueryBuilderInterface $queryBuilder, bool $toString = false): array
    {
        return $queryBuilder->find($toString);
    }
}
