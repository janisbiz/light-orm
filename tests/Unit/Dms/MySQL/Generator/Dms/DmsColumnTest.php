<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\Generator\Dms;

use Janisbiz\LightOrm\Dms\MySQL\Generator\Dms\DmsColumn;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Dms\DmsException;
use PHPUnit\Framework\TestCase;

class DmsColumnTest extends TestCase
{
    const COLUMN_NAME = 'name_snake_case';
    const COLUMN_NAME_PHP = 'NameSnakeCase';
    const COLUMN_TYPE = 'varchar';
    const COLUMN_TYPE_INVALID = 'invalid';
    const COLUMN_TYPE_PHP = 'string';
    const COLUMN_NULLABLE = true;
    const COLUMN_KEY = 'PRI';
    const COLUMN_DEFAULT = 'string';
    const COLUMN_DEFAULT_PHP = 'string';
    const COLUMN_EXTRA = 'auto_increment';

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

    /**
     * @dataProvider getPhpNameData
     *
     * @param string $name
     * @param string $phpName
     */
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

    /**
     *
     * @return array
     */
    public function getPhpNameData()
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

    /**
     * @dataProvider defaultData
     *
     * @param string $default
     * @param string $dmsType
     * @param string $phpDefaultType
     */
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

    /**
     *
     * @return array
     */
    public function defaultData()
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

    /**
     * @dataProvider isNullableData
     *
     * @param bool $isNullable
     * @param bool $expectedIsNullable
     */
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

    /**
     *
     * @return array
     */
    public function isNullableData()
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

    /**
     * @dataProvider getKeyData
     *
     * @param string $key
     * @param string $expectedKey
     */
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

    /**
     *
     * @return array
     */
    public function getKeyData()
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

    /**
     * @dataProvider typeData
     *
     * @param array $dmsTypes
     * @param $phpType
     */
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

    /**
     *
     * @return array
     */
    public function typeData()
    {
        return [
            [
                [
                    'bigint',
                    'int',
                    'timestamp',
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
