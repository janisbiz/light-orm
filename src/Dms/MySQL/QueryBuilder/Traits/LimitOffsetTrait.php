<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;

trait LimitOffsetTrait
{
    protected ?int $limit;

    protected ?int $offset;

    /**
     * @throws QueryBuilderException
     */
    public function limit(int $limit): static
    {
        if (0 >= $limit) {
            throw new QueryBuilderException('You must pass $limit to limit method!');
        }

        $this->limit = (int) $limit;

        return $this;
    }

    /**
     * @throws QueryBuilderException
     */
    public function offset(int $offset): static
    {
        if (0 > $offset) {
            throw new QueryBuilderException('You must pass $offset to offset method!');
        }

        if (empty($this->limit)) {
            throw new QueryBuilderException('You must set LIMIT before calling offset method!');
        }

        $this->offset = (int) $offset;

        return $this;
    }

    /**
     * @return $this
     */
    public function limitWithOffset(int $limit, int $offset): static
    {
        return $this->limit($limit)->offset($offset);
    }

    protected function buildLimitQueryPart(): ?string
    {
        return empty($this->limit) ? null : sprintf('%s %d', ConditionEnum::LIMIT, $this->limit);
    }

    protected function buildOffsetQueryPart(): ?string
    {
        return empty($this->offset) ? null : sprintf('%s %d', ConditionEnum::OFFSET, $this->offset);
    }
}
