<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\Generator\Dms;

use Janisbiz\LightOrm\Generator\Dms\DmsColumnInterface;

class DmsColumn implements DmsColumnInterface
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
    public function __construct(
        string $name,
        string $type,
        bool $nullable,
        string $key,
        string $default,
        ?string $extra
    ) {
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPhpName(): string
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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     * @throws DmsException
     */
    public function getPhpType(): string
    {
        \preg_match('/^(?<type>\w+)/', $this->getType(), $matches);
        switch ($matches['type']) {
            case static::TYPE_BIGINT:
            case static::TYPE_INT:
            case static::TYPE_TIMESTAMP:
            case static::TYPE_TINYINT:
                return static::PHP_TYPE_INTEGER;

            case static::TYPE_FLOAT:
                return static::PHP_TYPE_DOUBLE;

            case static::TYPE_CHAR:
            case static::TYPE_DATE:
            case static::TYPE_DATETIME:
            case static::TYPE_JSON:
            case static::TYPE_LONGTEXT:
            case static::TYPE_MEDIUMTEXT:
            case static::TYPE_TEXT:
            case static::TYPE_VARCHAR:
                return static::PHP_TYPE_STRING;
        }

        throw new DmsException(\sprintf(
            'Could not determine type for column "%s" with type "%s"',
            $this->getName(),
            $this->getType()
        ));
    }

    /**
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getDefault(): string
    {
        return $this->default;
    }

    /**
     * @return string
     */
    public function getPhpDefaultType(): string
    {
        $default = $this->getDefault();

        if (null !== $default) {
            switch ($this->getPhpType()) {
                case static::PHP_TYPE_INTEGER:
                    $default = (int) $default;

                    break;

                case static::PHP_TYPE_DOUBLE:
                    $default = (double) $default;

                    break;

                case static::PHP_TYPE_STRING:
                    $default = (string) $default;

                    break;
            }
        }

        return \mb_strtolower(\gettype($default));
    }

    /**
     * @return null|string
     */
    public function getExtra(): ?string
    {
        return $this->extra;
    }
}
