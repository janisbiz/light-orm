<?php

namespace Janisbiz\LightOrm\Dms\MySQL\Repository;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilder;
use Janisbiz\LightOrm\Entity\EntityInterface;
use Janisbiz\LightOrm\Generator\Writer\WriterInterface;
use Janisbiz\LightOrm\QueryBuilder\QueryBuilderInterface;
use Janisbiz\LightOrm\Repository\AbstractRepository as BaseAbstractRepository;

abstract class AbstractRepository extends BaseAbstractRepository
{
    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param bool $toString
     *
     * @throws \Exception
     * @return string|EntityInterface
     */
    public function insert(QueryBuilderInterface $queryBuilder, $toString = false)
    {
        if (!($entity = $queryBuilder->getEntity())) {
            throw new \Exception(
                'Cannot perform insert on query without entity! Please create query builder with entity.'
            );
        }

        $this->addEntityInsertQuery($queryBuilder, $entity);

        if (true === $toString) {
            return $queryBuilder->toString();
        }

        $connection = $this->getConnection();

        if (true === ($commit = !$connection->inTransaction())) {
            $connection->beginTransaction();
        }

        try {
            $statement = $connection->prepare($queryBuilder->buildQuery());
            $statement->execute($queryBuilder->bindValueData());
        } catch (\Exception $e) {
            $connection->rollBack();

            throw $e;
        }

        if (!empty($primaryKeysAutoIncrement = $entity->primaryKeysAutoIncrement())) {
            $entityData = &$entity->data();

            foreach ($primaryKeysAutoIncrement as $primaryKeyAutoIncrement) {
                $entityData[$primaryKeyAutoIncrement] = (int) $connection->lastInsertId($entity::TABLE_NAME);
            }
        }

        if (true === $commit) {
            $connection->commit();
        }

        return $entity;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param bool $toString
     *
     * @throws \Exception
     * @return string|EntityInterface
     */
    public function insertIgnore(QueryBuilder $queryBuilder, $toString = false)
    {
        return $this->insert($queryBuilder, $toString);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param bool $toString
     *
     * @throws \Exception
     * @return string|EntityInterface
     */
    public function replace(QueryBuilder $queryBuilder, $toString = false)
    {
        return $this->insert($queryBuilder, $toString);
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param bool $toString
     *
     * @return null|string|EntityInterface
     */
    public function findOne(QueryBuilderInterface $queryBuilder, $toString = false)
    {
        if (true === $toString) {
            return $queryBuilder->toString();
        }

        $connection = $this->getConnection();

        try {
            $statement = $connection->prepare($queryBuilder->buildQuery());
            $statement->execute($queryBuilder->bindData());
            $statement->setFetchMode(
                \PDO::FETCH_CLASS,
                ($entity = $queryBuilder->getEntity()) ? \get_class($entity) : $this->getModelClass(),
                [
                    false,
                ]
            );
        } catch (\Exception $e) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw $e;
        }

        return $statement->fetch() ? : null;
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param bool $toString
     *
     * @return string|array
     */
    public function find(QueryBuilderInterface $queryBuilder, $toString = false)
    {
        if (true === $toString) {
            return $queryBuilder->toString();
        }

        $connection = $this->getConnection();

        try {
            $statement = $connection->prepare($queryBuilder->buildQuery());
            $statement->execute($queryBuilder->bindData());
            $statement->setFetchMode(
                \PDO::FETCH_CLASS,
                ($entity = $queryBuilder->getEntity()) ? \get_class($entity) : $this->getModelClass(),
                [
                    false,
                ]
            );
        } catch (\Exception $e) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw $e;
        }

        return $statement->fetchAll() ?: [];
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param bool $toString
     *
     * @return null|string|bool|EntityInterface
     */
    public function update(QueryBuilderInterface $queryBuilder, $toString = false)
    {
        if (($entity = $queryBuilder->getEntity()) && !$this->addEntityUpdateQuery($queryBuilder, $entity, $toString)) {
            return $entity;
        }

        if (true === $toString) {
            return $queryBuilder->toString();
        }

        $connection = $this->getConnection();

        if (true === ($commit = !$connection->inTransaction())) {
            $connection->beginTransaction();
        }

        try {
            $connection->exec('SET SESSION SQL_SAFE_UPDATES = 1;');

            $statement = $connection->prepare($queryBuilder->buildQuery());
            $statement->execute($queryBuilder->bindData());

            $connection->exec('SET SESSION SQL_SAFE_UPDATES = 0;');
        } catch (\Exception $e) {
            $connection->rollBack();

            throw $e;
        }

        if (true === $commit) {
            $connection->commit();
        }

        return $entity ?: true;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param bool $toString
     *
     * @return null|string|bool|EntityInterface
     */
    public function updateIgnore(QueryBuilder $queryBuilder, $toString = false)
    {
        return $this->update($queryBuilder, $toString);
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param bool $toString
     *
     * @return string|bool
     */
    public function delete(QueryBuilderInterface $queryBuilder, $toString = false)
    {
        if (($entity = $queryBuilder->getEntity()) && !$this->addEntityDeleteQuery($queryBuilder, $entity)) {
            return false;
        }

        if (true === $toString) {
            return $queryBuilder->toString();
        }

        $connection = $this->getConnection();

        if (true === ($commit = !$connection->inTransaction())) {
            $connection->beginTransaction();
        }

        try {
            $connection->exec('SET SESSION SQL_SAFE_UPDATES = 1;');

            $statement = $connection->prepare($queryBuilder->buildQuery());
            $statement->execute($queryBuilder->bindData());

            $connection->exec('SET SESSION SQL_SAFE_UPDATES = 0;');
        } catch (\Exception $e) {
            $connection->rollBack();

            throw $e;
        }

        if (true === $commit) {
            $connection->commit();
        }

        return true;
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param bool $toString
     *
     * @return int
     */
    public function count(QueryBuilderInterface $queryBuilder, $toString = false)
    {
        /** Flush all columns and use "true" as a value to avoid column conflicts and get actual result count */
        $queryBuilder->column(true, true);

        $queryBuilderCount = (new QueryBuilder($this))
            ->command($queryBuilder->commandData())
            ->column('COUNT(*)')
            ->table(\sprintf('(%s) AS total_count', $queryBuilder->buildQuery()))
            ->bind($queryBuilder->bindData())
        ;

        if ($toString === true) {
            return $queryBuilderCount->toString();
        }

        $connection = $this->getConnection();

        try {
            $statement = $connection->prepare($queryBuilderCount->buildQuery());
            $statement->execute($queryBuilderCount->bindData());
        } catch (\Exception $e) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw $e;
        }

        return $statement->fetchColumn(0);
    }

    /**
     * @param EntityInterface|null $entity
     *
     * @return QueryBuilder
     */
    protected function createQueryBuilder(EntityInterface $entity = null)
    {
        return (new QueryBuilder($this, $entity))
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
    protected function addEntityUpdateQuery(QueryBuilderInterface $queryBuilder, EntityInterface $entity, $toString)
    {
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
        $entityData = &$entity->data();

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
}
