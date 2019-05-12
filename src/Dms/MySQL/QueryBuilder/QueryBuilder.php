<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder;

use Janisbiz\LightOrm\Dms\MySQL\Repository\AbstractRepository;
use Janisbiz\LightOrm\Entity\EntityInterface;
use Janisbiz\LightOrm\Dms\MySQL\Enum\CommandEnum;

class QueryBuilder implements QueryBuilderInterface, TraitsInterface
{
    use Traits;

    /**
     * @var AbstractRepository
     */
    protected $repository;

    /**
     * @var null|EntityInterface
     */
    protected $entity;

    /**
     * @param AbstractRepository $repository
     * @param EntityInterface|null $entity
     */
    public function __construct(AbstractRepository $repository, EntityInterface $entity = null)
    {
        $this->repository = $repository;
        $this->entity = $entity;
    }

    /**
     * @param bool $toString
     *
     * @return string|EntityInterface
     */
    public function insert($toString = false)
    {
        $this->command(CommandEnum::INSERT_INTO);

        return $this->repository->insert($this, $toString);
    }

    /**
     * @param bool $toString
     *
     * @return string|EntityInterface
     */
    public function insertIgnore($toString = false)
    {
        $this->command(CommandEnum::INSERT_IGNORE_INTO);

        return $this->repository->insertIgnore($this, $toString);
    }

    /**
     * @param bool $toString
     *
     * @return string|EntityInterface
     */
    public function replace($toString = false)
    {
        $this->command(CommandEnum::REPLACE_INTO);

        return $this->repository->replace($this, $toString);
    }

    /**
     * @param bool $toString
     *
     * @return string|EntityInterface[]
     */
    public function find($toString = false)
    {
        $this->command(CommandEnum::SELECT);

        return $this->repository->find($this, $toString);
    }

    /**
     * @param bool $toString
     *
     * @return null|string|EntityInterface
     */
    public function findOne($toString = false)
    {
        $this->command(CommandEnum::SELECT);

        return $this->repository->findOne($this, $toString);
    }

    /**
     * @param bool $toString
     *
     * @return null|string|bool|EntityInterface
     */
    public function update($toString = false)
    {
        $this->command(CommandEnum::UPDATE);

        return $this->repository->update($this, $toString);
    }

    /**
     * @param bool $toString
     *
     * @return null|string|bool|EntityInterface
     */
    public function updateIgnore($toString = false)
    {
        $this->command(CommandEnum::UPDATE_IGNORE);

        return $this->repository->updateIgnore($this, $toString);
    }

    /**
     * @param bool $toString
     *
     * @return string|bool
     */
    public function delete($toString = false)
    {
        $this->command(CommandEnum::DELETE);

        return $this->repository->delete($this, $toString);
    }

    /**
     * @param bool $toString
     *
     * @return int
     */
    public function count($toString = false)
    {
        $this->command(CommandEnum::SELECT);

        return $this->repository->count($this, $toString);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function buildQuery()
    {
        if (empty($this->command)) {
            throw new \Exception('Could not build query, as there is no command provided!');
        }

        switch ($this->command) {
            case CommandEnum::INSERT_IGNORE_INTO:
            case CommandEnum::INSERT_INTO:
            case CommandEnum::REPLACE_INTO:
                $queryParts = [
                    $this->buildCommandQueryPart(),
                    $this->buildTableQueryPart(),
                    $this->buildValueQueryPart(),
                    $this->buildOnDuplicateKeyUpdateQueryPart(),
                ];

                break;

            case CommandEnum::SELECT:
                if (!empty($this->unionAll)) {
                    $queryParts = [
                        $this->buildUnionAllQueryPart(),
                        $this->buildOrderByQueryPart(),
                        $this->buildLimitQueryPart(),
                        $this->buildOffsetQueryPart(),
                    ];

                    break;
                }

                $queryParts = [
                    $this->buildCommandQueryPart(),
                    $this->buildColumnQueryPart(),
                    $this->buildFromQueryPart(),
                    $this->buildJoinQueryPart(),
                    $this->buildWhereQueryPart(),
                    $this->buildGroupByQueryPart(),
                    $this->buildHavingQueryPart(),
                    $this->buildOrderByQueryPart(),
                    $this->buildLimitQueryPart(),
                    $this->buildOffsetQueryPart(),
                ];

                break;

            case CommandEnum::UPDATE:
            case CommandEnum::UPDATE_IGNORE:
                if (empty($this->set)) {
                    throw new \PDOException('Cannot perform UPDATE action without SET condition!');
                }

                if (empty($this->where)) {
                    throw new \PDOException('Cannot perform UPDATE action without WHERE condition!');
                }

                $queryParts = [
                    $this->buildCommandQueryPart(),
                    $this->buildTableQueryPart(),
                    $this->buildJoinQueryPart(),
                    $this->buildSetQueryPart(),
                    $this->buildWhereQueryPart(),
                    $this->buildLimitQueryPart(),
                    $this->buildOffsetQueryPart(),
                ];

                break;

            case CommandEnum::DELETE:
                if (empty($this->where)) {
                    throw new \PDOException('Cannot perform DELETE action without WHERE condition!');
                }

                $queryParts = [
                    $this->buildCommandQueryPart(),
                    $this->buildTableQueryPart(),
                    $this->buildFromQueryPart(),
                    $this->buildJoinQueryPart(),
                    $this->buildWhereQueryPart(),
                    $this->buildLimitQueryPart(),
                    $this->buildOffsetQueryPart(),
                ];

                break;

            default:
                throw new \Exception(\sprintf(
                    'Could not build query, as there is no valid(%s) command provided!',
                    $this->command
                ));
        }

        return \trim(\implode(' ', \array_filter($queryParts)));
    }

    /**
     * @return null|EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->generateSqlExecuteString($this->buildQuery());
    }

    /**
     * @param $query
     *
     * @return string
     */
    protected function generateSqlExecuteString($query)
    {
        foreach ($this->bind as $bindName => $bindValue) {
            $query = \preg_replace(
                \sprintf('/\:\b%s\b/', \preg_quote($bindName)),
                \is_string($bindValue) ? \sprintf('\'%s\'', $bindValue) : $bindValue,
                $query
            );
        }

        foreach ($this->bindValue as $bindValueName => $bindValueValue) {
            $query = \preg_replace(
                \sprintf('/\:\b%s\b/', \preg_quote($bindValueName)),
                \is_string($bindValueValue) ? \sprintf('\'%s\'', $bindValueValue) : $bindValueValue,
                $query
            );
        }

        return $query;
    }
}
