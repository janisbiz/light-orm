<?php

namespace Janisbiz\LightOrm\Generator\Dms;

class DmsColumn
{
    const TYPE_BIGINT = 'bigint';
    const TYPE_CHAR = 'char';
    const TYPE_DATE = 'date';
    const TYPE_DATETIME = 'datetime';
    const TYPE_FLOAT = 'float';
    const TYPE_INT = 'int';
    const TYPE_JSON = 'json';
    const TYPE_LONGTEXT = 'longtext';
    const TYPE_MEDIUMTEXT = 'mediumtext';
    const TYPE_TEXT = 'text';
    const TYPE_TIMESTAMP = 'timestamp';
    const TYPE_TINYINT = 'tinyint';
    const TYPE_VARCHAR = 'varchar';

    const PHP_TYPE_ARRAY = 'array';
    const PHP_TYPE_BOOLEAN = 'boolean';
    const PHP_TYPE_DOUBLE = 'double';
    const PHP_TYPE_INTEGER = 'integer';
    const PHP_TYPE_NULL = 'null';
    const PHP_TYPE_OBJECT = 'object';
    const PHP_TYPE_RESOURCE = 'resource';
    const PHP_TYPE_STRING = 'string';

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $nullable;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $default;

    /**
     * @var string
     */
    protected $extra;

    /**
     * @param string $name
     * @param string $type
     * @param bool $nullable
     * @param string $key
     * @param string $default
     * @param null|string $extra
     */
    public function __construct($name, $type, $nullable, $key, $default, $extra)
    {
        $this->name = $name;
        $this->type = $type;
        $this->nullable = $nullable;
        $this->key = $key;
        $this->default = $default;
        $this->extra = $extra;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPhpName()
    {
        return \ucfirst(\preg_replace_callback(
            '/[^a-z0-9]+(?<name>\w{1})/i',
            function ($matches) {
                return \strtoupper($matches['name']);
            },
            $this->getName()
        ));
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @throws \Exception
     * @return string
     */
    public function getPhpType()
    {
        \preg_match('/^(?<type>\w+)/', $this->getType(), $matches);
        switch ($matches['type']) {
            case self::TYPE_BIGINT:
            case self::TYPE_INT:
            case self::TYPE_TIMESTAMP:
            case self::TYPE_TINYINT:
                return self::PHP_TYPE_INTEGER;

            case self::TYPE_FLOAT:
                return self::PHP_TYPE_DOUBLE;

            case self::TYPE_CHAR:
            case self::TYPE_DATE:
            case self::TYPE_DATETIME:
            case self::TYPE_JSON:
            case self::TYPE_LONGTEXT:
            case self::TYPE_MEDIUMTEXT:
            case self::TYPE_TEXT:
            case self::TYPE_VARCHAR:
                return self::PHP_TYPE_STRING;
        }

        throw new \Exception(\sprintf(
            'Could not determine type for column "%s" with type "%s"',
            $this->getName(),
            $this->getType()
        ));
    }

    /**
     * @return bool
     */
    public function isNullable()
    {
        return $this->nullable;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @return string
     */
    public function getPhpDefaultType()
    {
        $default = $this->getDefault();

        if (null !== $default) {
            switch ($this->getPhpType()) {
                case self::PHP_TYPE_INTEGER:
                    $default = (int) $default;

                    break;

                case self::PHP_TYPE_DOUBLE:
                    $default = (double) $default;

                    break;

                case self::PHP_TYPE_STRING:
                    $default = (string) $default;

                    break;
            }
        }

        return \mb_strtolower(\gettype($default));
    }

    /**
     * @return null|string
     */
    public function getExtra()
    {
        return $this->extra;
    }
}
