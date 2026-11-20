<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\CommandEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderInterface;

trait UnionTrait
{
    protected array $unionAll = [];

    /**
     * @throws QueryBuilderException
     */
    public function unionAll(QueryBuilderInterface $queryBuilder): static
    {
        if (CommandEnum::SELECT != $queryBuilder->commandData()) {
            throw new QueryBuilderException(sprintf(
                '$queryBuilder should be with valid command! Valid command: "%s"',
                CommandEnum::SELECT
            ));
        }

        $this->unionAll[] = sprintf('(%s)', $queryBuilder->buildQuery());

        $this->bind($queryBuilder->bindData());

        return $this;
    }

    protected function buildUnionAllQueryPart(): ?string
    {
        return empty($this->unionAll) ? null : implode(' UNION ALL ', $this->unionAll);
    }
}
