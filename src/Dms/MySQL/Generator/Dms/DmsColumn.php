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
    const PHP_TYPE_BOOLEAN = 'bool';
    const PHP_TYPE_FLOAT = 'float';
    const PHP_TYPE_INTEGER = 'int';
    const PHP_TYPE_NULL = 'null';
    const PHP_TYPE_OBJECT = 'object';
    const PHP_TYPE_RESOURCE = 'resource';
    const PHP_TYPE_STRING = 'string';

    const REGEX_PHP_NAME = '/[^a-z0-9]+(?<name>\w{1})/i';
    const REGEX_PHP_TYPE = '/^(?<type>\w+)/';

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
            static::REGEX_PHP_NAME,
            fn($matches) => \strtoupper($matches['name']),
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
        \preg_match(static::REGEX_PHP_TYPE, $this->getType(), $matches);
        return match ($matches['type']) {
            static::TYPE_BIGINT, static::TYPE_INT, static::TYPE_TIMESTAMP, static::TYPE_TINYINT
                => static::PHP_TYPE_INTEGER,
            static::TYPE_FLOAT => static::PHP_TYPE_FLOAT,
            static::TYPE_CHAR, static::TYPE_DATE, static::TYPE_DATETIME, static::TYPE_JSON, static::TYPE_LONGTEXT,
            static::TYPE_MEDIUMTEXT, static::TYPE_TEXT, static::TYPE_VARCHAR => static::PHP_TYPE_STRING,
            default => throw new DmsException(\sprintf(
                'Could not determine type for column "%s" with type "%s"',
                $this->getName(),
                $this->getType()
            )),
        };
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
        $default = empty($this->getDefault()) ? null : $this->getDefault();

        if (null !== $default) {
            switch ($this->getPhpType()) {
                case static::PHP_TYPE_INTEGER:
                    return static::PHP_TYPE_INTEGER;

                case static::PHP_TYPE_FLOAT:
                    return static::PHP_TYPE_FLOAT;

                case static::PHP_TYPE_STRING:
                    return static::PHP_TYPE_STRING;
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
