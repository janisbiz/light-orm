<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;

trait ValueTrait
{
    protected array $value = [];

    protected array $bindValue = [];

    public function value(string $column, string|int|float|null $value): static
    {
        $columnNormalised = sprintf(
            '%s_Value',
            implode(
                '_',
                array_map(
                    fn($columnPart) => mb_convert_case($columnPart, MB_CASE_TITLE),
                    explode('.', $column)
                )
            )
        );

        $this->value[$column] = sprintf(':%s', $columnNormalised);
        $this->bindValue([
            $columnNormalised => $value,
        ]);

        return $this;
    }

    public function bindValue(array $bindValue = []): static
    {
        $this->bindValue = array_merge($this->bindValue, $bindValue);

        return $this;
    }

    public function bindValueData(): array
    {
        return $this->bindValue;
    }

    protected function buildValueQueryPart(): ?string
    {
        return empty($this->value)
            ? null
            : sprintf(
                '(%s) %s (%s)',
                implode(', ', array_keys($this->value)),
                ConditionEnum::VALUES,
                implode(', ', $this->value)
            )
        ;
    }
}
