<?php

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder;

use Janisbiz\LightOrm\Entity\EntityInterface;
use Janisbiz\LightOrm\Dms\MySQL\Enum\CommandEnum;
use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\Traits;
use Janisbiz\LightOrm\QueryBuilder\QueryBuilderInterface;
use Janisbiz\LightOrm\Repository\AbstractRepository;

class QueryBuilder implements QueryBuilderInterface
{
    use Traits;

    private $query = [
        'command' => null,
        'column' => null,
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

    /**
     * @var AbstractRepository
     */
    private $repository;

    /**
     * @var null|EntityInterface
     */
    private $entity;

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
     * @return mixed
     */
    public function insert($toString = false)
    {
        $this->setCommand(CommandEnum::INSERT_INTO);

        return $this->repository->insert($this, $toString);
    }

    /**
     * @param bool $toString
     *
     * @return mixed
     */
    public function insertIgnore($toString = false)
    {
        $this->setCommand(CommandEnum::INSERT_IGNORE_INTO);

        return $this->repository->insertIgnore($this, $toString);
    }

    /**
     * @param bool $toString
     *
     * @return mixed
     */
    public function replace($toString = false)
    {
        $this->setCommand(CommandEnum::REPLACE_INTO);

        return $this->repository->replace($this, $toString);
    }

    /**
     * @param bool $toString
     *
     * @return string|array
     */
    public function find($toString = false)
    {
        $this->setCommand(CommandEnum::SELECT);

        return $this->repository->find($this, $toString);
    }

    /**
     * @param bool $toString
     *
     * @return mixed
     */
    public function findOne($toString = false)
    {
        $this->setCommand(CommandEnum::SELECT);

        return $this->repository->findOne($this, $toString);
    }

    /**
     * @param bool $toString
     *
     * @return mixed
     */
    public function update($toString = false)
    {
        $this->setCommand(CommandEnum::UPDATE);

        return $this->repository->update($this, $toString);
    }

    /**
     * @param bool $toString
     *
     * @return mixed
     */
    public function updateIgnore($toString = false)
    {
        $this->setCommand(CommandEnum::UPDATE_IGNORE);

        return $this->repository->updateIgnore($this, $toString);
    }

    /**
     * @param bool $toString
     *
     * @return mixed
     */
    public function delete($toString = false)
    {
        $this->setCommand(CommandEnum::DELETE);

        return $this->repository->delete($this, $toString);
    }

    public function buildQuery()
    {
        $this->resetQuery();

        $this->query['command'] = $this->command;

        if (!empty($this->column)) {
            $this->query['column'] = \implode(', ', $this->column);
        } else {
            $this->query['column'] = '*';
        }

        if (!empty($this->where)) {
            $this->query['where'] = \sprintf(
                '%s %s',
                ConditionEnum::WHERE,
                \implode(' AND ', \array_unique($this->where))
            );
        }

        if (!empty($this->join)) {
            $this->query['join'] = \implode(' ', $this->join);
        }

        if (!empty($this->groupBy)) {
            $this->query['groupBy'] = \sprintf('%s %s', ConditionEnum::GROUP_BY, \implode(', ', $this->groupBy));
        }

        if (!empty($this->orderBy)) {
            $this->query['orderBy'] = \sprintf('%s %s', ConditionEnum::ORDER_BY, \implode(', ', $this->orderBy));
        }

        if (!empty($this->limit) && !empty($this->offset)) {
            $this->query['limit'] = \sprintf('%s %d', ConditionEnum::LIMIT, $this->limit);
            $this->query['offset'] = \sprintf('%s %d', ConditionEnum::OFFSET, $this->offset);
        } elseif (!empty($this->limit)) {
            $this->query['limit'] = \sprintf('%s %d', ConditionEnum::LIMIT, $this->limit);
        }

        if (!empty($this->set)) {
            $this->query['set'] = \sprintf(
                '%s %s',
                ConditionEnum::SET,
                \implode(', ', \array_unique($this->set))
            );
        }

        if (!empty($this->value)) {
            $this->query['value'] = \sprintf(
                '(%s) %s (%s)',
                \implode(', ', \array_keys($this->value)),
                ConditionEnum::VALUES,
                \implode(', ', $this->value)
            );
        }

        if (!empty($this->onDuplicateKeyUpdate)) {
            $this->query['onDuplicateKeyUpdate'] = \sprintf(
                'ON DUPLICATE KEY UPDATE %s',
                \implode(', ', $this->onDuplicateKeyUpdate)
            );
        }

        if (!empty($this->having)) {
            $this->query['having'] = \sprintf(
                '%s %s',
                ConditionEnum::HAVING,
                \implode(' AND ', \array_unique($this->having))
            );
        }

        if (!empty($this->unionAll)) {
            $this->query['unionAll'] = \implode(' UNION ALL ', $this->unionAll);
        }

        switch ($this->query['command']) {
            case CommandEnum::SELECT:
                if (!empty($this->from)) {
                    $this->query['table'] = \sprintf(
                        '%s %s',
                        ConditionEnum::FROM,
                        \implode(', ', $this->from)
                    );
                }

                $userQueries = [
                    $this->query['command'],
                    $this->query['column'],
                    $this->query['table'],
                    $this->query['join'],
                    $this->query['where'],
                    $this->query['groupBy'],
                    $this->query['having'],
                    $this->query['orderBy'],
                    $this->query['limit'],
                    $this->query['offset'],
                ];

                if (isset($this->query['unionAll']) && \mb_strlen($this->query['unionAll']) > 0) {
                    $userQueries = [
                        $this->query['unionAll'],
                        $this->query['orderBy'],
                        $this->query['limit'],
                        $this->query['offset'],
                    ];
                }

                return \trim(\implode(' ', \array_filter($userQueries)));

            case CommandEnum::UPDATE:
            case CommandEnum::UPDATE_IGNORE:
                if (!empty($this->from)) {
                    $this->query['table'] = $this->from[0];
                }

                if (!$this->query['where'] || \mb_strlen(\trim($this->query['where'])) == 0) {
                    throw new \PDOException('Cannot perform UPDATE action without WHERE condition!');
                }

                if (!$this->query['set'] || \mb_strlen(\trim($this->query['set'])) == 0) {
                    throw new \PDOException('Cannot perform UPDATE action without SET condition!');
                }

                $userQueries = [
                    $this->query['command'],
                    $this->query['table'],
                    $this->query['join'],
                    $this->query['set'],
                    $this->query['where'],
                    $this->query['limit'],
                    $this->query['offset'],
                ];

                return \trim(\implode(' ', \array_filter($userQueries)));

            case CommandEnum::DELETE:
                if (!empty($this->from)) {
                    $this->query['table'] = \sprintf('%s FROM %s', $this->from[0], $this->from[0]);
                }

                if (!$this->query['where'] || \mb_strlen(\trim($this->query['where'])) == 0) {
                    throw new \PDOException('Cannot perform DELETE action without WHERE condition!');
                }

                $userQueries = [
                    $this->query['command'],
                    $this->query['table'],
                    $this->query['join'],
                    $this->query['where'],
                ];

                return \trim(\implode(' ', \array_filter($userQueries)));

            case CommandEnum::INSERT_IGNORE_INTO:
            case CommandEnum::INSERT_INTO:
            case CommandEnum::REPLACE_INTO:
                if (!empty($this->from)) {
                    $this->query['table'] = $this->from[0];
                }

                $userQueries = [
                    $this->query['command'],
                    $this->query['table'],
                    $this->query['value'],
                    $this->query['onDuplicateKeyUpdate'],
                ];

                return \trim(\implode(' ', \array_filter($userQueries)));
        }

        throw new \Exception('Could not build query, as there is no command provided!');
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
    private function resetQuery()
    {
        $this->query = [
            'command' => $this->query['command'],
            'column' => null,
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
    private function generateSqlExecuteString($query)
    {
        foreach ($this->bind as $bindName => $bindValue) {
            $query = \preg_replace(
                \sprintf('/\:\b%s\b/', \preg_quote($bindName)),
                \is_string($bindValue) ? \sprintf('\'%s\'', $bindValue) : $bindValue,
                $query
            );
        }

        foreach ($this->bindValue as $bindName => $bindValue) {
            $query = \preg_replace(
                \sprintf('/\:\b%s\b/', \preg_quote($bindName)),
                \is_string($bindValue) ? \sprintf('\'%s\'', $bindValue) : $bindValue,
                $query
            );
        }

        return $query;
    }
}
