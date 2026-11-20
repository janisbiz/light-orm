<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;

trait OnDuplicateKeyUpdateTrait
{
    protected array $onDuplicateKeyUpdate = [];

    /**
     * @throws QueryBuilderException
     */
    public function onDuplicateKeyUpdate(string $column, string|int|float|null $value): static
    {
        if (empty($column)) {
            throw new QueryBuilderException('You must pass $column to onDuplicateKeyUpdate function!');
        }

        $columnNormalised = sprintf(
            '%s_OnDuplicateKeyUpdate',
            implode(
                '_',
                array_map(
                    fn($columnPart) => mb_convert_case($columnPart, MB_CASE_TITLE),
                    explode('.', $column)
                )
            )
        );

        $this->onDuplicateKeyUpdate = array_merge(
            $this->onDuplicateKeyUpdate,
            [
                $column => sprintf('%s = :%s', $column, $columnNormalised),
            ]
        );

        $this->bindValue([
            $columnNormalised => $value,
        ]);

        return $this;
    }

    protected function buildOnDuplicateKeyUpdateQueryPart(): ?string
    {
        return empty($this->onDuplicateKeyUpdate)
            ? null
            : sprintf('ON DUPLICATE KEY UPDATE %s', implode(', ', $this->onDuplicateKeyUpdate))
        ;
    }
}
