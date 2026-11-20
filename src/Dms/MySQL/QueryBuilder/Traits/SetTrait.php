<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;

trait SetTrait
{
    protected array $set = [];

    public function set(string $column, string|int|float|null $value): static
    {
        $columnNormalised = sprintf(
            '%s_Update',
            implode(
                '_',
                array_map(
                    fn($columnPart) => mb_convert_case($columnPart, MB_CASE_TITLE),
                    explode('.', $column)
                )
            )
        );

        $this->set[$column] = sprintf('%s = :%s', $column, $columnNormalised);
        $this->bind([
            $columnNormalised => $value,
        ]);

        return $this;
    }

    protected function buildSetQueryPart(): ?string
    {
        return empty($this->set)
            ? null
            : sprintf('%s %s', ConditionEnum::SET, implode(', ', array_unique($this->set)))
        ;
    }
}
