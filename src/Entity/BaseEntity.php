<?php

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
    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        } else {
            return null;
        }
    }

    /**
     * @deprecated 30.04.2019 Should have proper getters & setters
     *
     * @param string $name
     * @param string|int|bool $value
     */
    public function __set($name, $value)
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
     * @throws \Exception
     * @return bool|null|string|$this
     */
    public function __call($name, $arguments)
    {
        $methodName = \substr($name, 0, 3);
        \preg_match_all('/[A-Z0-9][^A-Z0-9]*/', $name, $results);

        switch ($methodName) {
            case 'get':
                if (\array_key_exists(\mb_strtolower(\implode('_', $results[0])), $this->data)) {
                    $variable = $this->data[\strtolower(\implode('_', $results[0]))];

                    if ($arguments && $arguments[0] === true) {
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

        throw new \Exception(\sprintf('Call to undefined method %s::%s()', __CLASS__, $name));
    }

    /**
     * @return array
     */
    public function &data()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function &dataOriginal()
    {
        return $this->dataOriginal;
    }

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->isNew;
    }

    /**
     * @return bool
     */
    public function isSaved()
    {
        return $this->isSaved;
    }

    /**
     * @return string[]
     */
    public function primaryKeys()
    {
        return $this->primaryKeys;
    }

    /**
     * @return string[]
     */
    public function primaryKeysAutoIncrement()
    {
        return $this->primaryKeysAutoIncrement;
    }

    /**
     * @return string[]
     */
    public function columns()
    {
        return $this->columns;
    }
}
