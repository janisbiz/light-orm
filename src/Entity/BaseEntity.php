<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Entity;

class BaseEntity implements EntityInterface
{
    protected array $primaryKeys = [];

    protected array $primaryKeysAutoIncrement = [];

    protected array $columns = [];

    protected array $data = [];

    protected array $dataOriginal = [];

    protected bool $isNew = true;

    protected bool $isSaved = false;

    /**
     * @deprecated 30.04.2019 Should have proper getters & setters
     */
    public function __get(string $name): mixed
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        return null;
    }

    /**
     * @deprecated 30.04.2019 Should have proper getters & setters
     */
    public function __set(string $name, string|int|float|null $value)
    {
        $this->data[$name] = $value;

        /** Construct values, for update queries */
        $this->dataOriginal[$name] ??= $this->data[$name];
    }

    /**
     * @throws EntityException
     */
    public function __call(string $name, ?array $arguments): mixed
    {
        $methodName = substr($name, 0, 3);
        preg_match_all('/[A-Z0-9][^A-Z0-9]*/', $name, $results);

        switch ($methodName) {
            case 'get':
                if (array_key_exists(mb_strtolower(implode('_', $results[0])), $this->data)) {
                    $variable = $this->data[strtolower(implode('_', $results[0]))];

                    if ($arguments && $arguments[0] === true && is_string($variable)) {
                        /** Stripping tags, output for user */
                        return nl2br(htmlspecialchars(trim($variable), ENT_QUOTES, 'UTF-8'));
                    }

                    /** Returning unchanged variable */
                    return $variable;
                }

                break;

            case 'set':
                if (!isset($arguments[0]) || $arguments[0] === '') {
                    $arguments[0] = null;
                }

                $this->data[strtolower(implode('_', $results[0]))] = $arguments[0];

                return $this;
        }

        throw new EntityException(sprintf('Call to undefined method %s::%s()', self::class, $name));
    }

    /**
     * @throws EntityException
     */
    public function &data(?string $key = null): array|string|int|float|null
    {
        if (null !== $key && is_string($key)) {
            if (!array_key_exists($key, $this->data)) {
                throw new EntityException(sprintf('There is no key "%s" present in data!', $key));
            }

            return $this->data[$key];
        }

        return $this->data;
    }

    /**
     * @throws EntityException
     */
    public function &dataOriginal(?string $key = null): array|string|int|float|null
    {
        if (null !== $key && is_string($key)) {
            if (!array_key_exists($key, $this->data)) {
                throw new EntityException(sprintf('There is no key "%s" present in data original!', $key));
            }

            return $this->data[$key];
        }

        return $this->dataOriginal;
    }

    public function isNew(): bool
    {
        return $this->isNew;
    }

    public function isSaved(): bool
    {
        return $this->isSaved;
    }

    public function primaryKeys(): array
    {
        return $this->primaryKeys;
    }

    public function primaryKeysAutoIncrement(): array
    {
        return $this->primaryKeysAutoIncrement;
    }

    public function columns(): array
    {
        return $this->columns;
    }
}
