<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\OnDuplicateKeyUpdateTrait;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\ValueTrait;

class OnDuplicateKeyUpdateTraitTest extends AbstractTraitTestCase
{
    const ON_DUPLICATE_KEY_UPDATE_DEFAULT = [
        'column1' => ':Column1_OnDuplicateKeyUpdate',
    ];
    const ON_DUPLICATE_KEY_UPDATE_BIND_VALUE_DEFAULT = [
        'Column1_OnDuplicateKeyUpdate' => 'value1',
    ];
    const ON_DUPLICATE_KEY_UPDATE_BIND_VALUE_OVERRIDE = [
        'Column1_OnDuplicateKeyUpdate' => 'value1_override',
    ];
    const ON_DUPLICATE_KEY_UPDATE_BIND_VALUE = [
        'Column2_OnDuplicateKeyUpdate' => 'value2',
    ];

    /**
     * @var ValueTrait|OnDuplicateKeyUpdateTrait
     */
    private $onDuplicateKeyUpdateTraitClass;
    
    public function setUp()
    {
        $this->onDuplicateKeyUpdateTraitClass = new class (
            OnDuplicateKeyUpdateTraitTest::ON_DUPLICATE_KEY_UPDATE_BIND_VALUE_DEFAULT,
            OnDuplicateKeyUpdateTraitTest::ON_DUPLICATE_KEY_UPDATE_DEFAULT
        ) {
            use ValueTrait;
            use OnDuplicateKeyUpdateTrait;

            /**
             * @param array $bindValueDataDefault
             * @param array $onDuplicateKeyUpdateDataDefault
             */
            public function __construct(array $bindValueDataDefault, array $onDuplicateKeyUpdateDataDefault)
            {
                $this->bindValue = $bindValueDataDefault;
                $this->onDuplicateKeyUpdate = $onDuplicateKeyUpdateDataDefault;
            }

            /**
             * @return array
             */
            public function onDuplicateKeyUpdateData(): array
            {
                return $this->onDuplicateKeyUpdate;
            }

            public function clearOnDuplicateKeyUpdateData()
            {
                $this->onDuplicateKeyUpdate = [];
            }

            /**
             * @return null|string
             */
            public function buildOnDuplicateKeyUpdateQueryPartPublic(): ?string
            {
                return $this->buildOnDuplicateKeyUpdateQueryPart();
            }
        };
    }

    /**
     * @dataProvider onDuplicateKeyUpdateData
     *
     * @param string $column
     * @param null|int|string|double $value
     */
    public function testOnDuplicateKeyUpdate(string $column, $value)
    {
        $object = $this->onDuplicateKeyUpdateTraitClass->onDuplicateKeyUpdate($column, $value);
        $this->assertObjectUsesTrait(ValueTrait::class, $object);
        $this->assertObjectUsesTrait(OnDuplicateKeyUpdateTrait::class, $object);

        $this->assertEquals(
            \array_merge(
                static::ON_DUPLICATE_KEY_UPDATE_DEFAULT,
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
            $this->onDuplicateKeyUpdateTraitClass->onDuplicateKeyUpdateData()
        );
        $this->assertEquals(
            \array_merge(
                static::ON_DUPLICATE_KEY_UPDATE_BIND_VALUE_DEFAULT,
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
            $this->onDuplicateKeyUpdateTraitClass->bindValueData()
        );
    }

    public function testBuildOnDuplicateKeyUpdateWhenEmptyColumnPassed()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage('You must pass $column to onDuplicateKeyUpdate function!');

        $this->onDuplicateKeyUpdateTraitClass->onDuplicateKeyUpdate('', null);
    }

    /**
     * @dataProvider onDuplicateKeyUpdateData
     *
     * @param string $column
     * @param null|int|string|double $value
     */
    public function testBuildOnDuplicateKeyUpdateQueryPart(string $column, $value)
    {
        $this->onDuplicateKeyUpdateTraitClass->onDuplicateKeyUpdate($column, $value);

        $this->assertEquals(
            \sprintf(
                'ON DUPLICATE KEY UPDATE %s',
                \implode(', ', $this->onDuplicateKeyUpdateTraitClass->onDuplicateKeyUpdateData())
            ),
            $this->onDuplicateKeyUpdateTraitClass->buildOnDuplicateKeyUpdateQueryPartPublic()
        );
    }

    public function testBuildOnDuplicateKeyUpdateQueryPartWhenEmpty()
    {
        $this->onDuplicateKeyUpdateTraitClass->clearOnDuplicateKeyUpdateData();

        $this->assertEquals(null, $this->onDuplicateKeyUpdateTraitClass->buildOnDuplicateKeyUpdateQueryPartPublic());
    }

    /**
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
