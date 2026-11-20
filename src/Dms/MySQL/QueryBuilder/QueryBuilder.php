<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder;

use Closure;
use Janisbiz\LightOrm\Entity\EntityInterface;
use Janisbiz\LightOrm\Dms\MySQL\Enum\CommandEnum;

class QueryBuilder implements QueryBuilderInterface
{
    use Traits;

    public function __construct(
        protected Closure $repositoryCallback,
        protected ?EntityInterface $entity = null
    ) {
    }

    public function insert(bool $toString = false): string|EntityInterface
    {
        $this->command(CommandEnum::INSERT_INTO);

        return call_user_func($this->repositoryCallback, 'insert', $this, $toString);
    }

    public function insertIgnore(bool $toString = false): string|EntityInterface
    {
        $this->command(CommandEnum::INSERT_IGNORE_INTO);

        return call_user_func($this->repositoryCallback, 'insertIgnore', $this, $toString);
    }

    public function replace(bool $toString = false): string|EntityInterface
    {
        $this->command(CommandEnum::REPLACE_INTO);

        return call_user_func($this->repositoryCallback, 'replace', $this, $toString);
    }

    public function find(bool $toString = false): string|array
    {
        $this->command(CommandEnum::SELECT);

        return call_user_func($this->repositoryCallback, 'find', $this, $toString);
    }

    public function findOne(bool $toString = false): string|EntityInterface|null
    {
        $this->command(CommandEnum::SELECT);

        return call_user_func($this->repositoryCallback, 'findOne', $this, $toString);
    }

    public function update(bool $toString = false): string|EntityInterface|bool
    {
        $this->command(CommandEnum::UPDATE);

        return call_user_func($this->repositoryCallback, 'update', $this, $toString);
    }

    public function updateIgnore(bool $toString = false): string|EntityInterface
    {
        $this->command(CommandEnum::UPDATE_IGNORE);

        return call_user_func($this->repositoryCallback, 'updateIgnore', $this, $toString);
    }

    public function delete(bool $toString = false): string|bool
    {
        $this->command(CommandEnum::DELETE);

        return call_user_func($this->repositoryCallback, 'delete', $this, $toString);
    }

    public function count(bool $toString = false): string|int
    {
        $this->command(CommandEnum::SELECT);

        return call_user_func($this->repositoryCallback, 'count', $this, $toString);
    }

    /**
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
                throw new QueryBuilderException(sprintf(
                    'Could not build query, as there is no valid(%s) command provided!',
                    $this->command
                ));
        }

        return trim(implode(' ', array_filter($queryParts)));
    }

    public function getEntity(): ?EntityInterface
    {
        return $this->entity;
    }

    public function toString(): string
    {
        return $this->generateSqlExecuteString($this->buildQuery());
    }

    protected function generateSqlExecuteString(string $query): string
    {
        foreach ($this->bind as $bindName => $bindValue) {
            $query = preg_replace(
                sprintf('/\:\b%s\b/', preg_quote($bindName)),
                is_string($bindValue) ? sprintf('\'%s\'', $bindValue) : (string)$bindValue,
                $query
            );
        }

        foreach ($this->bindValue as $bindValueName => $bindValueValue) {
            $query = preg_replace(
                sprintf('/\:\b%s\b/', preg_quote($bindValueName)),
                is_string($bindValueValue) ? sprintf('\'%s\'', $bindValueValue) : (string)$bindValueValue,
                $query
            );
        }

        return $query;
    }
}
