<?php

namespace Janisbiz\LightOrm\Repository;

use Janisbiz\LightOrm\Entity\EntityInterface;
use Janisbiz\LightOrm\Dms\MySQL\Connection\Connection;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Writer\BaseModelClassWriter;
use Janisbiz\LightOrm\ConnectionPool;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder;

abstract class AbstractRepository
{
    /**
     * @param QueryBuilder $queryBuilder
     * @param bool $toString
     *
     * @throws \Exception
     * @return string|EntityInterface
     */
    public function insert(QueryBuilder $queryBuilder, $toString = false)
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

        if ($commit = !$connection->inTransaction()) {
            $connection->beginTransaction();
        }

        $statement = $connection->prepare($queryBuilder->buildQuery());
        $statement->execute($queryBuilder->bindValueData());

        if (!empty($primaryKeysAutoIncrement = $entity->primaryKeysAutoIncrement())) {
            $entityData = &$entity->data();

            foreach ($primaryKeysAutoIncrement as $primaryKeyAutoIncrement) {
                $entityData[$primaryKeyAutoIncrement] = (int) $connection->lastInsertId();
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
     * @param QueryBuilder $queryBuilder
     * @param bool $toString
     *
     * @return null|string|EntityInterface
     */
    public function findOne(QueryBuilder $queryBuilder, $toString = false)
    {
        if (true === $toString) {
            return $queryBuilder->toString();
        }

        $statement = $this->getConnection()->prepare($queryBuilder->buildQuery());
        $statement->execute($queryBuilder->bindData());
        $statement->setFetchMode(
            \PDO::FETCH_CLASS,
            ($entity = $queryBuilder->getEntity()) ? \get_class($entity) : $this->getModelClass(),
            [
                false,
            ]
        );

        return $statement->fetch();
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param bool $toString
     *
     * @return string|array
     */
    public function find(QueryBuilder $queryBuilder, $toString = false)
    {
        if (true === $toString) {
            return $queryBuilder->toString();
        }

        $statement = $this->getConnection()->prepare($queryBuilder->buildQuery());
        $statement->execute($queryBuilder->bindData());
        $statement->setFetchMode(
            \PDO::FETCH_CLASS,
            ($entity = $queryBuilder->getEntity()) ? \get_class($entity) : $this->getModelClass(),
            [
                false,
            ]
        );

        return $statement->fetchAll() ?: [];
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param bool $toString
     *
     * @return null|string|bool|EntityInterface
     */
    public function update(QueryBuilder $queryBuilder, $toString = false)
    {
        if (($entity = $queryBuilder->getEntity()) && !$this->addEntityUpdateQuery($queryBuilder, $entity)) {
            return $entity;
        }

        if (true === $toString) {
            return $queryBuilder->toString();
        }

        $connection = $this->getConnection();

        if ($commit = !$connection->inTransaction()) {
            $connection->beginTransaction();
        }

        $connection->exec('SET SESSION SQL_SAFE_UPDATES = 1;');

        $statement = $connection->prepare($queryBuilder->buildQuery());
        $statement->execute($queryBuilder->bindData());

        $connection->exec('SET SESSION SQL_SAFE_UPDATES = 0;');

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
     * @param QueryBuilder $queryBuilder
     * @param bool $toString
     *
     * @return string|bool
     */
    public function delete(QueryBuilder $queryBuilder, $toString = false)
    {
        if (($entity = $queryBuilder->getEntity()) && !$this->addEntityDeleteQuery($queryBuilder, $entity)) {
            return false;
        }

        if (true === $toString) {
            return $queryBuilder->toString();
        }

        $connection = $this->getConnection();

        if ($commit = !$connection->inTransaction()) {
            $connection->beginTransaction();
        }

        $connection->exec('SET SESSION SQL_SAFE_UPDATES = 1;');

        $statement = $connection->prepare($queryBuilder->buildQuery());
        $statement->execute($queryBuilder->bindData());

        $connection->exec('SET SESSION SQL_SAFE_UPDATES = 0;');

        if (true === $commit) {
            $connection->commit();
        }

        return true;
    }

    /**
     * @param EntityInterface|null $entity
     *
     * @return QueryBuilder
     */
    protected function createQueryBuilder(EntityInterface $entity = null)
    {
        return (new QueryBuilder($this, $entity))
            ->from($this->getModelClassConstant(BaseModelClassWriter::CLASS_CONSTANT_TABLE_NAME))
        ;
    }

    /**
     * @return Connection
     */
    protected function getConnection()
    {
        return ConnectionPool::getConnectionStatic(
            $this->getModelClassConstant(BaseModelClassWriter::CLASS_CONSTANT_DATABASE_NAME)
        );
    }

    abstract protected function getModelClass();

    /**
     * @param string $constant
     *
     * @return string|int
     */
    private function getModelClassConstant($constant)
    {
        return \constant(\sprintf('%s::%s', $this->getModelClass(), $constant));
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param EntityInterface $entity
     *
     * @return $this
     */
    protected function addEntityInsertQuery(QueryBuilder $queryBuilder, EntityInterface $entity)
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
     * @param QueryBuilder $queryBuilder
     * @param EntityInterface $entity
     *
     * @return bool
     */
    protected function addEntityUpdateQuery(QueryBuilder $queryBuilder, EntityInterface $entity)
    {
        $performUpdate = false;
        $entityData = &$entity->data();

        if (!$entity->isNew() || true === $entity->isSaved()) {
            foreach ($entity->primaryKeys() as $primaryKey) {
                if (isset($entityData[$primaryKey]) && !\is_null($entityData[$primaryKey])) {
                    $queryBuilder->where(
                        \sprintf('%s.%s = :%s_WhereUpdate', $entity::TABLE_NAME, $primaryKey, $primaryKey),
                        [
                            \sprintf('%s_WhereUpdate', $primaryKey) => $entityData[$primaryKey],
                        ]
                    );
                }
            }
        } else {
            return $performUpdate;
        }

        $entityDataOriginal = &$entity->dataOriginal();

        foreach ($entity->columns() as $column) {
            if ($column != 'id' && \array_key_exists($column, $entityDataOriginal)) {
                /** Updating only what is needed to update, for faster queries, skipping equal values */
                if (isset($entityDataOriginal[$column]) && $entityDataOriginal[$column] == $entityData[$column]) {
                    continue;
                } elseif (false === $performUpdate) {
                    $performUpdate = true;
                }

                $queryBuilder->set(\sprintf('%s.%s', $entity::TABLE_NAME, $column), $entityData[$column]);
                $entityDataOriginal[$column] = $entityData[$column];
            }
        }

        return $performUpdate;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param EntityInterface $entity
     *
     * @return bool
     */
    protected function addEntityDeleteQuery(QueryBuilder $queryBuilder, EntityInterface $entity)
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