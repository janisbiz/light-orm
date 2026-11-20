<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\Generator\Dms;

use Janisbiz\LightOrm\Dms\MySQL\Generator\Dms\DmsColumn;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Dms\DmsException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DmsColumnTest extends TestCase
{
    const string COLUMN_NAME = 'name_snake_case';
    const string COLUMN_NAME_PHP = 'NameSnakeCase';
    const string COLUMN_TYPE = 'varchar';
    const string COLUMN_TYPE_INVALID = 'invalid';
    const string COLUMN_TYPE_PHP = 'string';
    const bool COLUMN_NULLABLE = true;
    const string COLUMN_KEY = 'PRI';
    const string COLUMN_DEFAULT = 'string';
    const string COLUMN_DEFAULT_PHP = 'string';
    const string COLUMN_EXTRA = 'auto_increment';

    public function testGetName()
    {
        $dmsColumn = new DmsColumn(
            static::COLUMN_NAME,
            static::COLUMN_TYPE,
            static::COLUMN_NULLABLE,
            static::COLUMN_KEY,
            static::COLUMN_DEFAULT,
            static::COLUMN_EXTRA
        );

        $this->assertEquals(static::COLUMN_NAME, $dmsColumn->getName());
    }

    public function testGetType()
    {
        $dmsColumn = new DmsColumn(
            static::COLUMN_NAME,
            static::COLUMN_TYPE,
            static::COLUMN_NULLABLE,
            static::COLUMN_KEY,
            static::COLUMN_DEFAULT,
            static::COLUMN_EXTRA
        );

        $this->assertEquals(static::COLUMN_TYPE, $dmsColumn->getType());
    }

    public function testGetExtra()
    {
        $dmsColumn = new DmsColumn(
            static::COLUMN_NAME,
            static::COLUMN_TYPE,
            static::COLUMN_NULLABLE,
            static::COLUMN_KEY,
            static::COLUMN_DEFAULT,
            static::COLUMN_EXTRA
        );

        $this->assertEquals(static::COLUMN_EXTRA, $dmsColumn->getExtra());
    }

    #[DataProvider('getPhpNameData')]
    public function testGetPhpName(string $name, string $phpName)
    {
        $dmsColumn = new DmsColumn(
            $name,
            static::COLUMN_TYPE,
            static::COLUMN_NULLABLE,
            static::COLUMN_KEY,
            static::COLUMN_DEFAULT,
            static::COLUMN_EXTRA
        );

        $this->assertEquals($phpName, $dmsColumn->getPhpName());
    }

    public static function getPhpNameData(): array
    {
        return [
            [
                'name_snake_case',
                'NameSnakeCase',
            ],
            [
                'name__snake__case',
                'NameSnakeCase',
            ],
            [
                'name-snake-case',
                'NameSnakeCase',
            ],
            [
                'name1-snake2-case3',
                'Name1Snake2Case3',
            ],
            [
                '1name-2snake-3case',
                '1name2snake3case',
            ],
            [
                'name',
                'Name',
            ],
        ];
    }

    public function testGetDefault()
    {
        $dmsColumn = new DmsColumn(
            static::COLUMN_NAME,
            static::COLUMN_TYPE,
            static::COLUMN_NULLABLE,
            static::COLUMN_KEY,
            static::COLUMN_DEFAULT,
            static::COLUMN_EXTRA
        );

        $this->assertEquals(static::COLUMN_DEFAULT, $dmsColumn->getDefault());
    }

    #[DataProvider('defaultData')]
    public function testGetPhpDefaultType(string $default, string $dmsType, string $phpDefaultType)
    {
        $dmsColumn = new DmsColumn(
            static::COLUMN_NAME,
            $dmsType,
            static::COLUMN_NULLABLE,
            static::COLUMN_KEY,
            $default,
            static::COLUMN_EXTRA
        );

        $this->assertEquals($phpDefaultType, $dmsColumn->getPhpDefaultType());
    }

    public static function defaultData(): array
    {
        return [
            [
                '1',
                'int',
                'int',
            ],
            [
                '1.1',
                'float',
                'float',
            ],
            [
                '1',
                'varchar',
                'string',
            ],
            [
                '',
                'varchar',
                'null',
            ],
        ];
    }

    #[DataProvider('isNullableData')]
    public function testIsNullable(bool $isNullable, bool $expectedIsNullable)
    {
        $dmsColumn = new DmsColumn(
            static::COLUMN_NAME,
            static::COLUMN_TYPE,
            $isNullable,
            static::COLUMN_KEY,
            static::COLUMN_DEFAULT,
            static::COLUMN_EXTRA
        );

        $this->assertEquals($expectedIsNullable, $dmsColumn->isNullable());
    }

    public static function isNullableData(): array
    {
        return [
            [
                true,
                true,
            ],
            [
                false,
                false,
            ]
        ];
    }

    #[DataProvider('getKeyData')]
    public function testGetKey(string $key, string $expectedKey)
    {
        $dmsColumn = new DmsColumn(
            static::COLUMN_NAME,
            static::COLUMN_TYPE,
            static::COLUMN_NULLABLE,
            $key,
            static::COLUMN_DEFAULT,
            static::COLUMN_EXTRA
        );

        $this->assertEquals($expectedKey, $dmsColumn->getKey());
    }

    public static function getKeyData(): array
    {
        return [
            [
                'PRI',
                'PRI',
            ],
            [
                'CUSTOM',
                'CUSTOM',
            ],
            [
                '',
                '',
            ],
        ];
    }

    #[DataProvider('typeData')]
    public function testGetPhpType(array $dmsTypes, string $phpType)
    {
        foreach ($dmsTypes as $dmsType) {
            $dmsColumn = new DmsColumn(
                static::COLUMN_NAME,
                $dmsType,
                static::COLUMN_NULLABLE,
                static::COLUMN_KEY,
                static::COLUMN_DEFAULT,
                static::COLUMN_EXTRA
            );

            $this->assertEquals($phpType, $dmsColumn->getPhpType());
        }
    }

    public static function typeData(): array
    {
        return [
            [
                [
                    'bigint',
                    'int',
                    'tinyint',
                ],
                'int'
            ],
            [
                [
                    'float',
                ],
                'float',
            ],
            [
                [
                    'char',
                    'date',
                    'datetime',
                    'json',
                    'longtext',
                    'mediumtext',
                    'text',
                    'varchar',
                    'timestamp',
                ],
                'string',
            ],
        ];
    }

    public function testGetPhpTypeInvalid()
    {
        $this->expectException(DmsException::class);
        $this->expectExceptionMessage('Could not determine type for column "name_snake_case" with type "invalid"');

        (new DmsColumn(
            static::COLUMN_NAME,
            static::COLUMN_TYPE_INVALID,
            static::COLUMN_NULLABLE,
            static::COLUMN_KEY,
            static::COLUMN_DEFAULT,
            static::COLUMN_EXTRA
        ))
            ->getPhpType()
        ;
    }
}
