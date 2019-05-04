<?php

namespace Janisbiz\LightOrm\Tests\Unit\Generator\Dms;

use Janisbiz\LightOrm\Generator\Dms\DmsColumn;
use PHPUnit\Framework\TestCase;

class DmsColumnTest extends TestCase
{
    const COLUMN_NAME = 'name_snake_case';
    const COLUMN_NAME_PHP = 'NameSnakeCase';
    const COLUMN_TYPE = 'string';
    const COLUMN_TYPE_INVALID = 'invalid';
    const COLUMN_TYPE_PHP = 'string';
    const COLUMN_NULLABLE = true;
    const COLUMN_KEY = 'PRI';
    const COLUMN_DEFAULT = 'string';
    const COLUMN_DEFAULT_PHP = 'string';
    const COLUMN_EXTRA = 'auto_increment';

    public function testGetName()
    {
        $column = new DmsColumn(
            self::COLUMN_NAME,
            self::COLUMN_TYPE,
            self::COLUMN_NULLABLE,
            self::COLUMN_KEY,
            self::COLUMN_DEFAULT,
            self::COLUMN_EXTRA
        );

        $this->assertEquals(self::COLUMN_NAME, $column->getName());
    }

    public function testGetType()
    {
        $column = new DmsColumn(
            self::COLUMN_NAME,
            self::COLUMN_TYPE,
            self::COLUMN_NULLABLE,
            self::COLUMN_KEY,
            self::COLUMN_DEFAULT,
            self::COLUMN_EXTRA
        );

        $this->assertEquals(self::COLUMN_TYPE, $column->getType());
    }

    public function testGetExtra()
    {
        $column = new DmsColumn(
            self::COLUMN_NAME,
            self::COLUMN_TYPE,
            self::COLUMN_NULLABLE,
            self::COLUMN_KEY,
            self::COLUMN_DEFAULT,
            self::COLUMN_EXTRA
        );

        $this->assertEquals(self::COLUMN_EXTRA, $column->getExtra());
    }

    /**
     * @dataProvider getPhpNameData
     *
     * @param string $name
     * @param string $phpName
     */
    public function testGetPhpName($name, $phpName)
    {
        $column = new DmsColumn(
            $name,
            self::COLUMN_TYPE,
            self::COLUMN_NULLABLE,
            self::COLUMN_KEY,
            self::COLUMN_DEFAULT,
            self::COLUMN_EXTRA
        );

        $this->assertEquals($phpName, $column->getPhpName());
    }

    /**
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
        $column = new DmsColumn(
            self::COLUMN_NAME,
            self::COLUMN_TYPE,
            self::COLUMN_NULLABLE,
            self::COLUMN_KEY,
            self::COLUMN_DEFAULT,
            self::COLUMN_EXTRA
        );

        $this->assertEquals(self::COLUMN_DEFAULT, $column->getDefault());
    }

    /**
     * @dataProvider defaultData
     *
     * @param int|double|string|array $default
     * @param string $dmsType
     * @param string $phpDefaultType
     */
    public function testGetPhpDefaultType($default, $dmsType, $phpDefaultType)
    {
        $column = new DmsColumn(
            self::COLUMN_NAME,
            $dmsType,
            self::COLUMN_NULLABLE,
            self::COLUMN_KEY,
            $default,
            self::COLUMN_EXTRA
        );

        $this->assertEquals($phpDefaultType, $column->getPhpDefaultType());
    }

    public function defaultData()
    {
        return [
            [
                1,
                'int',
                'integer',
            ],
            [
                1.1,
                'float',
                'double',
            ],
            [
                '1',
                'varchar',
                'string',
            ],
        ];
    }

    /**
     * @dataProvider isNullableData
     *
     * @param bool $isNullable
     * @param bool $expectedIsNullable
     */
    public function testIsNullable($isNullable, $expectedIsNullable)
    {
        $column = new DmsColumn(
            self::COLUMN_NAME,
            self::COLUMN_TYPE,
            $isNullable,
            self::COLUMN_KEY,
            self::COLUMN_DEFAULT,
            self::COLUMN_EXTRA
        );

        $this->assertEquals($expectedIsNullable, $column->isNullable());
    }

    /**
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
    public function testGetKey($key, $expectedKey)
    {
        $column = new DmsColumn(
            self::COLUMN_NAME,
            self::COLUMN_TYPE,
            self::COLUMN_NULLABLE,
            $key,
            self::COLUMN_DEFAULT,
            self::COLUMN_EXTRA
        );

        $this->assertEquals($expectedKey, $column->getKey());
    }

    /**
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
    public function testGetPhpType(array $dmsTypes, $phpType)
    {
        foreach ($dmsTypes as $dmsType) {
            $column = new DmsColumn(
                self::COLUMN_NAME,
                $dmsType,
                self::COLUMN_NULLABLE,
                self::COLUMN_KEY,
                self::COLUMN_DEFAULT,
                self::COLUMN_EXTRA
            );

            $this->assertEquals($phpType, $column->getPhpType());
        }
    }

    /**
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
                'integer'
            ],
            [
                [
                    'float',
                ],
                'double',
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



    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Could not determine type for column "name_snake_case" with type "invalid"
     */
    public function testGetPhpTypeInvalid()
    {
        (new DmsColumn(
            self::COLUMN_NAME,
            self::COLUMN_TYPE_INVALID,
            self::COLUMN_NULLABLE,
            self::COLUMN_KEY,
            self::COLUMN_DEFAULT,
            self::COLUMN_EXTRA
        ))
            ->getPhpType()
        ;
    }
}
