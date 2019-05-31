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

/**
 * @method ConnectionInterface getConnection()
 */
abstract class AbstractRepository extends BaseAbstractRepository
{
    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param bool $toString
     *
     * @return string|EntityInterface
     * @throws RepositoryException|\Exception
     */
    protected function insert(QueryBuilderInterface $queryBuilder, bool $toString = false)
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
        } catch (\Exception $e) {
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

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param bool $toString
     *
     * @return string|EntityInterface
     */
    protected function insertIgnore(QueryBuilderInterface $queryBuilder, bool $toString = false)
    {
        return $this->insert($queryBuilder, $toString);
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param bool $toString
     *
     * @return string|EntityInterface
     */
    protected function replace(QueryBuilderInterface $queryBuilder, bool $toString = false)
    {
        return $this->insert($queryBuilder, $toString);
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param bool $toString
     *
     * @return null|string
     * @throws \Exception
     */
    protected function findOne(QueryBuilderInterface $queryBuilder, bool $toString = false)
    {
        if (true === $toString) {
            return $queryBuilder->toString();
        }

        try {
            $statement = $this->prepareAndExecute($queryBuilder, $queryBuilder->bindData());
            $statement->setFetchMode(
                \PDO::FETCH_CLASS,
                ($entity = $queryBuilder->getEntity()) ? \get_class($entity) : $this->getModelClass(),
                [
                    false,
                ]
            );
        } catch (\Exception $e) {
            $this->rollback();

            throw $e;
        }

        return $statement->fetch() ? : null;
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param bool $toString
     *
     * @return array|string
     * @throws \Exception
     */
    protected function find(QueryBuilderInterface $queryBuilder, bool $toString = false)
    {
        if (true === $toString) {
            return $queryBuilder->toString();
        }

        try {
            $statement = $this->prepareAndExecute($queryBuilder, $queryBuilder->bindData());
            $statement->setFetchMode(
                \PDO::FETCH_CLASS,
                ($entity = $queryBuilder->getEntity()) ? \get_class($entity) : $this->getModelClass(),
                [
                    false,
                ]
            );
        } catch (\Exception $e) {
            $this->rollback();

            throw $e;
        }

        return $statement->fetchAll() ?: [];
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param bool $toString
     *
     * @return null|bool|EntityInterface|string
     * @throws \Exception
     */
    protected function update(QueryBuilderInterface $queryBuilder, bool $toString = false)
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
        } catch (\Exception $e) {
            $this->rollBack($connection);
            $connection->unsetSqlSafeUpdates();

            throw $e;
        }

        return $this->commit($connection) ? ($entity ?: true) : false;
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param bool $toString
     *
     * @return null|string|bool|EntityInterface
     */
    protected function updateIgnore(QueryBuilderInterface $queryBuilder, bool $toString = false)
    {
        return $this->update($queryBuilder, $toString);
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param bool $toString
     *
     * @return bool|string
     * @throws \Exception
     */
    protected function delete(QueryBuilderInterface $queryBuilder, bool $toString = false)
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
        } catch (\Exception $e) {
            $this->rollBack($connection);
            $connection->unsetSqlSafeUpdates();

            throw $e;
        }

        return $this->commit($connection);
    }

    /**
     * @param BaseQueryBuilderInterface $queryBuilder
     * @param bool $toString
     *
     * @return int
     * @throws RepositoryException|\Exception
     */
    protected function count(BaseQueryBuilderInterface $queryBuilder, bool $toString = false)
    {
        /** @var QueryBuilderInterface $queryBuilder */

        if (CommandEnum::SELECT !== $queryBuilder->commandData()) {
            throw new RepositoryException(\sprintf(
                'Command "%s" is not a valid command for count query! Use "%s" command to execute count query.',
                $queryBuilder->commandData(),
                CommandEnum::SELECT
            ));
        }

        /** Flush all columns and use "true" as a value to avoid column conflicts and get actual result count */
        $queryBuilder->column(true, true);

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
        } catch (\Exception $e) {
            $this->rollBack($connection);

            throw $e;
        }

        return $statement->fetchColumn(0);
    }

    /**
     * @param EntityInterface|null $entity
     *
     * @return QueryBuilderInterface
     */
    protected function createQueryBuilder(?EntityInterface $entity = null): BaseQueryBuilderInterface
    {
        return (new QueryBuilder($this->createRepositoryCallClosure(), $entity))
            ->column(\sprintf('%s.*', $this->getModelClassConstant(WriterInterface::CLASS_CONSTANT_TABLE_NAME)))
            ->table($this->getModelClassConstant(WriterInterface::CLASS_CONSTANT_TABLE_NAME))
        ;
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param EntityInterface $entity
     *
     * @return $this
     */
    protected function addEntityInsertQuery(QueryBuilderInterface $queryBuilder, EntityInterface $entity)
    {
        $entityColumns = $entity->columns();
        $entityData = &$entity->data();

        foreach ($entityColumns as $column) {
            if (\array_key_exists($column, $entityData)) {
                $queryBuilder->value($column, $entityData[$column]);
            }
        }

        return $this;
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param EntityInterface $entity
     * @param bool $toString
     *
     * @return bool
     */
    protected function addEntityUpdateQuery(
        QueryBuilderInterface $queryBuilder,
        EntityInterface $entity,
        bool $toString
    ) {
        $performUpdate = false;
        $entityData = &$entity->data();
        $entityDataOriginal = &$entity->dataOriginal();

        if (false === $entity->isNew() || true === $entity->isSaved()) {
            foreach ($entity->primaryKeys() as $primaryKey) {
                if (isset($entityDataOriginal[$primaryKey])) {
                    $queryBuilder->where(
                        \sprintf('%s.%s = :%s_WhereUpdate', $entity::TABLE_NAME, $primaryKey, $primaryKey),
                        [
                            \sprintf('%s_WhereUpdate', $primaryKey) => $entityDataOriginal[$primaryKey],
                        ]
                    );
                }
            }
        } else {
            return $performUpdate;
        }

        foreach ($entity->columns() as $column) {
            /** Updating only what is needed to update, for faster queries, skipping equal values */
            if (!\array_key_exists($column, $entityDataOriginal)
                || (isset($entityDataOriginal[$column]) && $entityDataOriginal[$column] == $entityData[$column])
            ) {
                continue;
            } elseif (false === $performUpdate) {
                $performUpdate = true;
            }

            $queryBuilder->set(\sprintf('%s.%s', $entity::TABLE_NAME, $column), $entityData[$column]);

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

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param EntityInterface $entity
     *
     * @return bool
     */
    protected function addEntityDeleteQuery(QueryBuilderInterface $queryBuilder, EntityInterface $entity)
    {
        $performDelete = false;
        $entityData = $entity->data();

        foreach ($entity->primaryKeys() as $primaryKey) {
            if (isset($entityData[$primaryKey]) && !\is_null($entityData[$primaryKey])) {
                if (false === $performDelete) {
                    $performDelete = true;
                }

                $primaryKeyNormalised = \sprintf(
                    '%s_Delete',
                    \implode(
                        '',
                        \array_map(
                            function ($columnPart) {
                                return \mb_convert_case($columnPart, MB_CASE_TITLE);
                            },
                            \array_merge(
                                \explode('.', $entity::TABLE_NAME),
                                [
                                    $primaryKey,
                                ]
                            )
                        )
                    )
                );

                $queryBuilder->where(
                    \sprintf('%s.%s = :%s', $entity::TABLE_NAME, $primaryKey, $primaryKeyNormalised),
                    [
                        $primaryKeyNormalised => $entityData[$primaryKey],
                    ]
                );
            }
        }

        return $performDelete;
    }

    /**
     * @param BaseQueryBuilderInterface $queryBuilder
     * @param int $pageSize
     * @param int $currentPage
     *
     * @return $this
     */
    protected function addPaginateQuery(
        BaseQueryBuilderInterface $queryBuilder,
        int $pageSize,
        int $currentPage
    ): BaseAbstractRepository {
        /** @var QueryBuilderInterface $queryBuilder */
        $queryBuilder->limitWithOffset($pageSize, $pageSize * $currentPage - $pageSize);

        return $this;
    }

    /**
     * @param BaseQueryBuilderInterface $queryBuilder
     * @param bool $toString
     *
     * @return EntityInterface[]
     */
    protected function getPaginateResult(BaseQueryBuilderInterface $queryBuilder, bool $toString = false): array
    {
        /** @var QueryBuilderInterface $queryBuilder */
        return $queryBuilder->find($toString);
    }
}
