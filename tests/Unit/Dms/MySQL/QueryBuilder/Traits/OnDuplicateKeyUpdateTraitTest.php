<?php

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\OnDuplicateKeyUpdateTrait;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\ValueTrait;

class OnDuplicateKeyUpdateTraitTest extends AbstractTraitTestCase
{
    use ValueTrait;
    use OnDuplicateKeyUpdateTrait;
    
    const ON_DUPLICATE_KEY_UPDATE_DEFAULT = [
        'column1' => ':Column1_OnDuplicateKeyUpdate',
    ];
    const ON_DUPLICATE_KEY_UPDATE_BIND_DEFAULT = [
        'Column1_OnDuplicateKeyUpdate' => 'value1',
    ];
    const ON_DUPLICATE_KEY_UPDATE_BIND_OVERRIDE = [
        'Column1_OnDuplicateKeyUpdate' => 'value1_override',
    ];
    const ON_DUPLICATE_KEY_UPDATE_BIND = [
        'Column2_OnDuplicateKeyUpdate' => 'value2',
    ];

    public function setUp()
    {
        $this->onDuplicateKeyUpdate = self::ON_DUPLICATE_KEY_UPDATE_DEFAULT;
        $this->bindValue = self::ON_DUPLICATE_KEY_UPDATE_BIND_DEFAULT;
    }

    /**
     * @dataProvider onDuplicateKeyUpdateData
     *
     * @param string $column
     * @param null|int|string|double $value
     */
    public function testOnDuplicateKeyUpdate($column, $value)
    {
        $object = $this->onDuplicateKeyUpdate($column, $value);
        $this->assertObjectUsesTrait(ValueTrait::class, $object);
        $this->assertObjectUsesTrait(OnDuplicateKeyUpdateTrait::class, $object);

        $this->assertEquals(
            \array_merge(
                self::ON_DUPLICATE_KEY_UPDATE_DEFAULT,
                \array_reduce(
                    \array_map(
                        function ($column) {
                            return [
                                $column => \sprintf(
                                    '%s = :%s_OnDuplicateKeyUpdate',
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
            $this->onDuplicateKeyUpdate
        );
        $this->assertEquals(
            \array_merge(
                self::ON_DUPLICATE_KEY_UPDATE_BIND_DEFAULT,
                \array_reduce(
                    \array_map(
                        function ($column, $value) {
                            return [
                                \sprintf(
                                    '%s_OnDuplicateKeyUpdate',
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

    /**
     * @dataProvider onDuplicateKeyUpdateData
     *
     * @param string $column
     * @param null|int|string|double $value
     */
    public function testBuildOnDuplicateKeyUpdateQueryPart($column, $value)
    {
        $this->onDuplicateKeyUpdate($column, $value);

        $this->assertEquals(
            \sprintf('ON DUPLICATE KEY UPDATE %s', \implode(', ', $this->onDuplicateKeyUpdate)),
            $this->buildOnDuplicateKeyUpdateQueryPart()
        );
    }

    public function testBuildOnDuplicateKeyUpdateQueryPartWhenEmpty()
    {
        $this->onDuplicateKeyUpdate = [];

        $this->assertEquals(null, $this->buildOnDuplicateKeyUpdateQueryPart());
    }

    /**
     * @codeCoverageIgnore
     *
     * @return array
     */
    public function onDuplicateKeyUpdateData()
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
