<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Entity;

class BaseEntity implements EntityInterface
{
    /**
     * @var array
     */
    protected $primaryKeys = [];

    /**
     * @var array
     */
    protected $primaryKeysAutoIncrement = [];

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $dataOriginal = [];

    /**
     * @var bool $isNew
     */
    protected $isNew = true;

    /**
     * @var bool $isSaved
     */
    protected $isSaved = false;

    /**
     * @deprecated 30.04.2019 Should have proper getters & setters
     *
     * @param string $name
     *
     * @return string|int|bool
     */
    public function __get(string $name)
    {
        if (\array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        return null;
    }

    /**
     * @deprecated 30.04.2019 Should have proper getters & setters
     *
     * @param string $name
     * @param string|int|bool $value
     */
    public function __set(string $name, $value)
    {
        $this->data[$name] = $value;

        /** Construct values, for update queries */
        if (!isset($this->dataOriginal[$name])) {
            $this->dataOriginal[$name] = $this->data[$name];
        }
    }

    /**
     * @param string $name
     * @param array|null $arguments
     *
     * @throws EntityException
     * @return bool|null|string|$this
     */
    public function __call(string $name, ?array $arguments)
    {
        $methodName = \substr($name, 0, 3);
        \preg_match_all('/[A-Z0-9][^A-Z0-9]*/', $name, $results);

        switch ($methodName) {
            case 'get':
                if (\array_key_exists(\mb_strtolower(\implode('_', $results[0])), $this->data)) {
                    $variable = $this->data[\strtolower(\implode('_', $results[0]))];

                    if ($arguments && $arguments[0] === true && \is_string($variable)) {
                        /** Stripping tags, output for user */
                        return \nl2br(\htmlspecialchars(\trim($variable), ENT_QUOTES, 'UTF-8'));
                    }

                    /** Returning unchanged variable */
                    return $variable;
                }

                break;

            case 'set':
                if (!isset($arguments[0]) || $arguments[0] === '') {
                    $arguments[0] = null;
                }

                $this->data[\strtolower(\implode('_', $results[0]))] = $arguments[0];

                return $this;
        }

        throw new EntityException(\sprintf('Call to undefined method %s::%s()', __CLASS__, $name));
    }

    /**
     * @param null|string $key
     *
     * @return null|array|string|int|double
     * @throws EntityException
     */
    public function &data(?string $key = null)
    {
        if (null !== $key && \is_string($key)) {
            if (!\array_key_exists($key, $this->data)) {
                throw new EntityException(\sprintf('There is no key "%s" present in data!', $key));
            }

            return $this->data[$key];
        }

        return $this->data;
    }

    /**
     * @param null|string $key
     *
     * @return null|array|string|int|double
     * @throws EntityException
     */
    public function &dataOriginal(?string $key = null)
    {
        if (null !== $key && \is_string($key)) {
            if (!\array_key_exists($key, $this->data)) {
                throw new EntityException(\sprintf('There is no key "%s" present in data original!', $key));
            }

            return $this->data[$key];
        }

        return $this->dataOriginal;
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->isNew;
    }

    /**
     * @return bool
     */
    public function isSaved(): bool
    {
        return $this->isSaved;
    }

    /**
     * @return string[]
     */
    public function primaryKeys(): array
    {
        return $this->primaryKeys;
    }

    /**
     * @return string[]
     */
    public function primaryKeysAutoIncrement(): array
    {
        return $this->primaryKeysAutoIncrement;
    }

    /**
     * @return string[]
     */
    public function columns(): array
    {
        return $this->columns;
    }
}
