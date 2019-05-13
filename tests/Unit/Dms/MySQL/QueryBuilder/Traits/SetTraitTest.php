<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\BindTrait;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\SetTrait;

class SetTraitTest extends AbstractTraitTestCase
{
    use BindTrait;
    use SetTrait;

    const SET_DEFAULT = [
        'column1' => 'column1 = :Column1_Update',
    ];
    const SET_BIND_DEFAULT = [
        'Column1_Update' => 'value1',
    ];

    public function setUp()
    {
        $this->bind = self::SET_BIND_DEFAULT;
        $this->set = self::SET_DEFAULT;
    }

    /**
     * @dataProvider setData
     *
     * @param string $column
     * @param string|int|double|null $value
     */
    public function testSet($column, $value)
    {
        $object = $this->set($column, $value);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(SetTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                self::SET_DEFAULT,
                \array_reduce(
                    \array_map(
                        function ($column) {
                            return [
                                $column => \sprintf(
                                    '%s = :%s_Update',
                                    $column,
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
            $this->set
        );
        $this->assertEquals(
            \array_merge(
                self::SET_BIND_DEFAULT,
                \array_reduce(
                    \array_map(
                        function ($column, $value) {
                            return [
                                \sprintf(
                                    '%s_Update',
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
            $this->bind
        );
    }

    /**
     * @dataProvider setData
     *
     * @param string $column
     * @param string|int|double|null $value
     */
    public function testBuildSetQueryPart($column, $value)
    {
        $this->set($column, $value);

        $this->assertEquals(
            \sprintf('%s %s', ConditionEnum::SET, \implode(', ', \array_unique($this->set))),
            $this->buildSetQueryPart()
        );
    }

    public function testBuildSetQueryPartWhenEmpty()
    {
        $this->set = [];

        $this->assertEquals(null, $this->buildSetQueryPart());
    }

    /**
     * @codeCoverageIgnore
     *
     * @return array
     */
    public function setData()
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
}
