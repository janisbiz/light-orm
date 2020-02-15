<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\TraitsInterface;

trait LimitOffsetTrait
{
    /**
     * @var null|int
     */
    protected $limit;

    /**
     * @var null|int
     */
    protected $offset;

    /**
     * @param int $limit
     *
     * @return $this|TraitsInterface
     * @throws QueryBuilderException
     */
    public function limit(int $limit)
    {
        if (0 >= $limit) {
            throw new QueryBuilderException('You must pass $limit to limit method!');
        }

        $this->limit = (int) $limit;

        return $this;
    }

    /**
     * @param int $offset
     *
     * @return $this|TraitsInterface
     * @throws QueryBuilderException
     */
    public function offset(int $offset)
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
     * @param int $limit
     * @param int $offset
     *
     * @return $this|TraitsInterface
     */
    public function limitWithOffset(int $limit, int $offset)
    {
        return $this->limit($limit)->offset($offset);
    }

    /**
     * @return null|string
     */
    protected function buildLimitQueryPart(): ?string
    {
        return empty($this->limit) ? null : \sprintf('%s %d', ConditionEnum::LIMIT, $this->limit);
    }

    /**
     * @return null|string
     */
    protected function buildOffsetQueryPart(): ?string
    {
        return empty($this->offset) ? null : \sprintf('%s %d', ConditionEnum::OFFSET, $this->offset);
    }
}
