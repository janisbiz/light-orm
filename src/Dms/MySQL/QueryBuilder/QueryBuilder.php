<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder;

use Janisbiz\LightOrm\Dms\MySQL\Repository\AbstractRepository;
use Janisbiz\LightOrm\Entity\EntityInterface;
use Janisbiz\LightOrm\Dms\MySQL\Enum\CommandEnum;
use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;

class QueryBuilder implements QueryBuilderInterface, TraitsInterface
{
    use Traits;

    protected $queryParts = [];

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
     * @return string
     * @throws \Exception
     */
    public function buildQuery()
    {
        if (empty($this->command)) {
            throw new \Exception('Could not build query, as there is no command provided!');
        }

        $this->resetQuery();

        $this->queryParts['command'] = $this->command;

        if (!empty($this->column)) {
            $this->queryParts['column'] = \implode(', ', $this->column);
        } else {
            $this->queryParts['column'] = '*';
        }

        if (!empty($this->where)) {
            $this->queryParts['where'] = \sprintf(
                '%s %s',
                ConditionEnum::WHERE,
                \implode(' AND ', \array_unique($this->where))
            );
        }

        if (!empty($this->join)) {
            $this->queryParts['join'] = \implode(' ', $this->join);
        }

        if (!empty($this->groupBy)) {
            $this->queryParts['groupBy'] = \sprintf('%s %s', ConditionEnum::GROUP_BY, \implode(', ', $this->groupBy));
        }

        if (!empty($this->orderBy)) {
            $this->queryParts['orderBy'] = \sprintf('%s %s', ConditionEnum::ORDER_BY, \implode(', ', $this->orderBy));
        }

        if (!empty($this->limit) && !empty($this->offset)) {
            $this->queryParts['limit'] = \sprintf('%s %d', ConditionEnum::LIMIT, $this->limit);
            $this->queryParts['offset'] = \sprintf('%s %d', ConditionEnum::OFFSET, $this->offset);
        } elseif (!empty($this->limit)) {
            $this->queryParts['limit'] = \sprintf('%s %d', ConditionEnum::LIMIT, $this->limit);
        }

        if (!empty($this->set)) {
            $this->queryParts['set'] = \sprintf(
                '%s %s',
                ConditionEnum::SET,
                \implode(', ', \array_unique($this->set))
            );
        }

        if (!empty($this->value)) {
            $this->queryParts['value'] = \sprintf(
                '(%s) %s (%s)',
                \implode(', ', \array_keys($this->value)),
                ConditionEnum::VALUES,
                \implode(', ', $this->value)
            );
        }

        if (!empty($this->onDuplicateKeyUpdate)) {
            $this->queryParts['onDuplicateKeyUpdate'] = \sprintf(
                'ON DUPLICATE KEY UPDATE %s',
                \implode(', ', $this->onDuplicateKeyUpdate)
            );
        }

        if (!empty($this->having)) {
            $this->queryParts['having'] = \sprintf(
                '%s %s',
                ConditionEnum::HAVING,
                \implode(' AND ', \array_unique($this->having))
            );
        }

        if (!empty($this->unionAll)) {
            $this->queryParts['unionAll'] = \implode(' UNION ALL ', $this->unionAll);
        }

        switch ($this->queryParts['command']) {
            case CommandEnum::INSERT_IGNORE_INTO:
            case CommandEnum::INSERT_INTO:
            case CommandEnum::REPLACE_INTO:
                if (!empty($this->table)) {
                    $this->queryParts['table'] = $this->table[0];
                }

                $userQueries = [
                    $this->queryParts['command'],
                    $this->queryParts['table'],
                    $this->queryParts['value'],
                    $this->queryParts['onDuplicateKeyUpdate'],
                ];

                return \trim(\implode(' ', \array_filter($userQueries)));

            case CommandEnum::SELECT:
                if (isset($this->queryParts['unionAll']) && \mb_strlen($this->queryParts['unionAll']) > 0) {
                    $userQueries = [
                        $this->queryParts['unionAll'],
                        $this->queryParts['orderBy'],
                        $this->queryParts['limit'],
                        $this->queryParts['offset'],
                    ];
                } else {
                    if (!empty($this->table)) {
                        $this->queryParts['from'] = \sprintf(
                            '%s %s',
                            ConditionEnum::FROM,
                            \implode(', ', $this->table)
                        );
                    }

                    $userQueries = [
                        $this->queryParts['command'],
                        $this->queryParts['column'],
                        $this->queryParts['from'],
                        $this->queryParts['join'],
                        $this->queryParts['where'],
                        $this->queryParts['groupBy'],
                        $this->queryParts['having'],
                        $this->queryParts['orderBy'],
                        $this->queryParts['limit'],
                        $this->queryParts['offset'],
                    ];
                }

                return \trim(\implode(' ', \array_filter($userQueries)));

            case CommandEnum::UPDATE:
            case CommandEnum::UPDATE_IGNORE:
                if (!empty($this->table)) {
                    $this->queryParts['table'] = $this->table[0];
                }

                if (!$this->queryParts['set'] || \mb_strlen(\trim($this->queryParts['set'])) == 0) {
                    throw new \PDOException('Cannot perform UPDATE action without SET condition!');
                }

                if (!$this->queryParts['where'] || \mb_strlen(\trim($this->queryParts['where'])) == 0) {
                    throw new \PDOException('Cannot perform UPDATE action without WHERE condition!');
                }

                $userQueries = [
                    $this->queryParts['command'],
                    $this->queryParts['table'],
                    $this->queryParts['join'],
                    $this->queryParts['set'],
                    $this->queryParts['where'],
                    $this->queryParts['limit'],
                    $this->queryParts['offset'],
                ];

                return \trim(\implode(' ', \array_filter($userQueries)));

            case CommandEnum::DELETE:
                if (!empty($this->table)) {
                    $this->queryParts['from'] = \sprintf('%s FROM %s', $this->table[0], $this->table[0]);
                }

                if (!$this->queryParts['where'] || \mb_strlen(\trim($this->queryParts['where'])) == 0) {
                    throw new \PDOException('Cannot perform DELETE action without WHERE condition!');
                }

                $userQueries = [
                    $this->queryParts['command'],
                    $this->queryParts['from'],
                    $this->queryParts['join'],
                    $this->queryParts['where'],
                    $this->queryParts['limit'],
                    $this->queryParts['offset'],
                ];

                return \trim(\implode(' ', \array_filter($userQueries)));
        }

        throw new \Exception(\sprintf(
            'Could not build query, as there is no valid(%s) command provided!',
            $this->command
        ));
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
     * @return $this
     */
    protected function resetQuery()
    {
        $this->queryParts = [
            'command' => null,
            'column' => null,
            'from' => null,
            'table' => null,
            'where' => null,
            'join' => null,
            'groupBy' => null,
            'orderBy' => null,
            'limit' => null,
            'offset' => null,
            'set' => null,
            'value' => null,
            'onDuplicateKeyUpdate' => null,
            'having' => null,
            'unionAll' => null,
        ];

        return $this;
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
