<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\ValueTrait;

class ValueTraitTest extends AbstractTraitTestCase
{
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

    /**
     * @var ValueTrait
     */
    private $valueTraitClass;

    public function setUp()
    {
        $this->valueTraitClass = new class (ValueTraitTest::VALUE_DEFAULT, ValueTraitTest::VALUE_BIND_DEFAULT) {
            use ValueTrait;

            /**
             * @param array $valueDataDefault
             * @param array $bindValueDataDefault
             */
            public function __construct(array $valueDataDefault, array $bindValueDataDefault)
            {
                $this->value = $valueDataDefault;
                $this->bindValue = $bindValueDataDefault;
            }

            /**
             * @return array
             */
            public function valueData(): array
            {
                return $this->value;
            }

            public function clearValueData()
            {
                $this->value = [];
            }

            /**
             * @return null|string
             */
            public function buildValueQueryPartPublic(): ?string
            {
                return $this->buildValueQueryPart();
            }
        };
    }

    /**
     * @dataProvider valueData
     *
     * @param string $column
     * @param null|int|string|double $value
     */
    public function testValue($column, $value)
    {
        $object = $this->valueTraitClass->value($column, $value);
        $this->assertObjectUsesTrait(ValueTrait::class, $object);

        $this->assertEquals(
            \array_merge(
                static::VALUE_DEFAULT,
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
            $this->valueTraitClass->valueData()
        );
        $this->assertEquals(
            \array_merge(
                static::VALUE_BIND_DEFAULT,
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
            $this->valueTraitClass->bindValueData()
        );
    }

    /**
     * @dataProvider valueData
     *
     * @param string $column
     * @param null|int|string|double $value
     */
    public function testBuildValueQueryPart($column, $value)
    {
        $this->valueTraitClass->value($column, $value);

        $this->assertEquals(
            \sprintf(
                '(%s) %s (%s)',
                \implode(', ', \array_keys($this->valueTraitClass->valueData())),
                ConditionEnum::VALUES,
                \implode(', ', $this->valueTraitClass->valueData())
            ),
            $this->valueTraitClass->buildValueQueryPartPublic()
        );
    }

    public function testBuildValueQueryPartWhenEmpty()
    {
        $this->valueTraitClass->clearValueData();

        $this->assertEquals(null, $this->valueTraitClass->buildValueQueryPartPublic());
    }

    /**
     *
     * @return array
     */
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
        $this->assertEquals(static::VALUE_BIND_DEFAULT, $this->valueTraitClass->bindValueData());

        $object = $this->valueTraitClass->bindValue(static::VALUE_BIND);
        $this->assertObjectUsesTrait(ValueTrait::class, $object);
        $this->assertEquals(
            \array_merge(static::VALUE_BIND_DEFAULT, static::VALUE_BIND),
            $this->valueTraitClass->bindValueData()
        );

        $object = $this->valueTraitClass->bindValue(static::VALUE_BIND_OVERRIDE);
        $this->assertObjectUsesTrait(ValueTrait::class, $object);
        $this->assertEquals(
            \array_merge(static::VALUE_BIND_DEFAULT, static::VALUE_BIND, static::VALUE_BIND_OVERRIDE),
            $this->valueTraitClass->bindValueData()
        );
    }

    public function testBindValueData()
    {
        $this->assertEquals(static::VALUE_BIND_DEFAULT, $this->valueTraitClass->bindValueData());
    }
}
