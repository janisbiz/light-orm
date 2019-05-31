<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder;

use Janisbiz\LightOrm\Entity\EntityInterface;
use Janisbiz\LightOrm\Dms\MySQL\Enum\CommandEnum;

class QueryBuilder implements QueryBuilderInterface
{
    use Traits;

    /**
     * @var \Closure
     */
    protected $repositoryCallback;

    /**
     * @var null|EntityInterface
     */
    protected $entity;

    /**
     * @param \Closure $repositoryCallback
     * @param EntityInterface|null $entity
     */
    public function __construct(\Closure $repositoryCallback, EntityInterface $entity = null)
    {
        $this->repositoryCallback = $repositoryCallback;
        $this->entity = $entity;
    }

    /**
     * @param bool $toString
     *
     * @return string|EntityInterface
     */
    public function insert(bool $toString = false)
    {
        $this->command(CommandEnum::INSERT_INTO);

        return \call_user_func($this->repositoryCallback, 'insert', $this, $toString);
    }

    /**
     * @param bool $toString
     *
     * @return string|EntityInterface
     */
    public function insertIgnore(bool $toString = false)
    {
        $this->command(CommandEnum::INSERT_IGNORE_INTO);

        return \call_user_func($this->repositoryCallback, 'insertIgnore', $this, $toString);
    }

    /**
     * @param bool $toString
     *
     * @return string|EntityInterface
     */
    public function replace(bool $toString = false)
    {
        $this->command(CommandEnum::REPLACE_INTO);

        return \call_user_func($this->repositoryCallback, 'replace', $this, $toString);
    }

    /**
     * @param bool $toString
     *
     * @return string|EntityInterface[]
     */
    public function find(bool $toString = false)
    {
        $this->command(CommandEnum::SELECT);

        return \call_user_func($this->repositoryCallback, 'find', $this, $toString);
    }

    /**
     * @param bool $toString
     *
     * @return null|string|EntityInterface
     */
    public function findOne(bool $toString = false)
    {
        $this->command(CommandEnum::SELECT);

        return \call_user_func($this->repositoryCallback, 'findOne', $this, $toString);
    }

    /**
     * @param bool $toString
     *
     * @return null|string|bool|EntityInterface
     */
    public function update(bool $toString = false)
    {
        $this->command(CommandEnum::UPDATE);

        return \call_user_func($this->repositoryCallback, 'update', $this, $toString);
    }

    /**
     * @param bool $toString
     *
     * @return null|string|bool|EntityInterface
     */
    public function updateIgnore(bool $toString = false)
    {
        $this->command(CommandEnum::UPDATE_IGNORE);

        return \call_user_func($this->repositoryCallback, 'updateIgnore', $this, $toString);
    }

    /**
     * @param bool $toString
     *
     * @return string|bool
     */
    public function delete(bool $toString = false)
    {
        $this->command(CommandEnum::DELETE);

        return \call_user_func($this->repositoryCallback, 'delete', $this, $toString);
    }

    /**
     * @param bool $toString
     *
     * @return int
     */
    public function count(bool $toString = false)
    {
        $this->command(CommandEnum::SELECT);

        return \call_user_func($this->repositoryCallback, 'count', $this, $toString);
    }

    /**
     * @return string
     * @throws QueryBuilderException
     */
    public function buildQuery(): string
    {
        if (empty($this->command)) {
            throw new QueryBuilderException('Could not build query, as there is no command provided!');
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
                    throw new QueryBuilderException('Cannot perform UPDATE action without SET condition!');
                }

                if (empty($this->where)) {
                    throw new QueryBuilderException('Cannot perform UPDATE action without WHERE condition!');
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
                    throw new QueryBuilderException('Cannot perform DELETE action without WHERE condition!');
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
                throw new QueryBuilderException(\sprintf(
                    'Could not build query, as there is no valid(%s) command provided!',
                    $this->command
                ));
        }

        return \trim(\implode(' ', \array_filter($queryParts)));
    }

    /**
     * @return null|EntityInterface
     */
    public function getEntity(): ?EntityInterface
    {
        return $this->entity;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->generateSqlExecuteString($this->buildQuery());
    }

    /**
     * @param $query
     *
     * @return string
     */
    protected function generateSqlExecuteString($query): string
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
