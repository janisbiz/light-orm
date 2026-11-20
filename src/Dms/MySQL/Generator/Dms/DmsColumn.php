<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\Generator\Dms;

use Janisbiz\LightOrm\Generator\Dms\DmsColumnInterface;

class DmsColumn implements DmsColumnInterface
{
    const string TYPE_BIGINT = 'bigint';
    const string TYPE_CHAR = 'char';
    const string TYPE_DATE = 'date';
    const string TYPE_DATETIME = 'datetime';
    const string TYPE_FLOAT = 'float';
    const string TYPE_INT = 'int';
    const string TYPE_JSON = 'json';
    const string TYPE_LONGTEXT = 'longtext';
    const string TYPE_MEDIUMTEXT = 'mediumtext';
    const string TYPE_TEXT = 'text';
    const string TYPE_TIMESTAMP = 'timestamp';
    const string TYPE_TINYINT = 'tinyint';
    const string TYPE_VARCHAR = 'varchar';

    const string PHP_TYPE_ARRAY = 'array';
    const string PHP_TYPE_BOOLEAN = 'bool';
    const string PHP_TYPE_FLOAT = 'float';
    const string PHP_TYPE_INTEGER = 'int';
    const string PHP_TYPE_NULL = 'null';
    const string PHP_TYPE_OBJECT = 'object';
    const string PHP_TYPE_RESOURCE = 'resource';
    const string PHP_TYPE_STRING = 'string';

    const string REGEX_PHP_NAME = '/[^a-z0-9]+(?<name>\w{1})/i';
    const string REGEX_PHP_TYPE = '/^(?<type>\w+)/';

    public function __construct(
        protected string $name,
        protected string $type,
        protected bool $nullable,
        protected string $key,
        protected string $default,
        protected ?string $extra
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPhpName(): string
    {
        return ucfirst(preg_replace_callback(
            static::REGEX_PHP_NAME,
            fn($matches) => strtoupper($matches['name']),
            $this->getName()
        ));
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @throws DmsException
     */
    public function getPhpType(): string
    {
        preg_match(static::REGEX_PHP_TYPE, $this->getType(), $matches);
        return match ($matches['type']) {
            static::TYPE_BIGINT, static::TYPE_INT, static::TYPE_TINYINT
                => static::PHP_TYPE_INTEGER,
            static::TYPE_FLOAT => static::PHP_TYPE_FLOAT,
            static::TYPE_CHAR, static::TYPE_DATE, static::TYPE_DATETIME, static::TYPE_JSON, static::TYPE_LONGTEXT,
            static::TYPE_MEDIUMTEXT, static::TYPE_TEXT, static::TYPE_VARCHAR,
            static::TYPE_TIMESTAMP => static::PHP_TYPE_STRING,
            default => throw new DmsException(sprintf(
                'Could not determine type for column "%s" with type "%s"',
                $this->getName(),
                $this->getType()
            )),
        };
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getDefault(): string
    {
        return $this->default;
    }

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

        return mb_strtolower(gettype($default));
    }

    public function getExtra(): ?string
    {
        return $this->extra;
    }
}
