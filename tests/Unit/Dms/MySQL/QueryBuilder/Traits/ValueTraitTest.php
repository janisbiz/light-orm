<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\ValueTrait;

class ValueTraitTest extends AbstractTraitTestCase
{
    use ValueTrait;

    const VALUE_DEFAULT = [
        'column1' => ':Column1_Value',
    ];
    const VALUE_BIND_DEFAULT = [
        'Column1_Value' => 'value1',
    ];
    const VALUE_BIND_OVERRIDE = [
        'Column1_Value' => 'value1_override',
    ];
    const VALUE_BIND = [
        'Column2_Value' => 'value2',
    ];

    public function setUp()
    {
        $this->value = self::VALUE_DEFAULT;
        $this->bindValue = self::VALUE_BIND_DEFAULT;
    }

    /**
     * @dataProvider valueData
     *
     * @param string $column
     * @param null|int|string|double $value
     */
    public function testValue($column, $value)
    {
        $object = $this->value($column, $value);
        $this->assertObjectUsesTrait(ValueTrait::class, $object);

        $this->assertEquals(
            \array_merge(
                self::VALUE_DEFAULT,
                \array_reduce(
                    \array_map(
                        function ($column) {
                            return [
                                $column => \sprintf(
                                    ':%s_Value',
                                    \implode(
                                        '_',
                                        \array_map(
                                            function ($columnPart) {
                                                return \mb_convert_case($columnPart, MB_CASE_TITLE);
                                            },
                                            \explode('.', $column)
                                        )
                                    )
                                ),
                            ];
                        },
                        [
                            $column
                        ]
                    ),
                    'array_merge',
                    []
                )
            ),
            $this->value
        );
        $this->assertEquals(
            \array_merge(
                self::VALUE_BIND_DEFAULT,
                \array_reduce(
                    \array_map(
                        function ($column, $value) {
                            return [
                                \sprintf(
                                    '%s_Value',
                                    \implode(
                                        '_',
                                        \array_map(
                                            function ($columnPart) {
                                                return \mb_convert_case($columnPart, MB_CASE_TITLE);
                                            },
                                            \explode('.', $column)
                                        )
                                    )
                                ) => $value,
                            ];
                        },
                        [
                            $column
                        ],
                        [
                            $value
                        ]
                    ),
                    'array_merge',
                    []
                )
            ),
            $this->bindValue
        );
    }

    public function valueData()
    {
        return [
            [
                'column2',
                'value2'
            ],
            [
                'column3',
                3
            ],
            [
                'column4',
                4.4
            ],
            [
                'column5',
                null
            ],
            [
                'table1.column2',
                'value2'
            ],
            [
                'table1.column3',
                3
            ],
            [
                'table1.column4',
                4.4
            ],
            [
                'table1.column5',
                null
            ],
        ];
    }

    public function testBindValue()
    {
        $this->assertEquals(self::VALUE_BIND_DEFAULT, $this->bindValue);

        $object = $this->bindValue(self::VALUE_BIND);
        $this->assertObjectUsesTrait(ValueTrait::class, $object);
        $this->assertEquals(\array_merge(self::VALUE_BIND_DEFAULT, self::VALUE_BIND), $this->bindValue);

        $object = $this->bindValue(self::VALUE_BIND_OVERRIDE);
        $this->assertObjectUsesTrait(ValueTrait::class, $object);
        $this->assertEquals(
            \array_merge(self::VALUE_BIND_DEFAULT, self::VALUE_BIND, self::VALUE_BIND_OVERRIDE),
            $this->bindValue
        );
    }

    public function testBindValueData()
    {
        $this->assertEquals(self::VALUE_BIND_DEFAULT, $this->bindValueData());
    }
}
