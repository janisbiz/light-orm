<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\Enum\ConditionEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\BindTrait;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\SetTrait;

class SetTraitTest extends AbstractTraitTestCase
{
    const SET_DEFAULT = [
        'column1' => 'column1 = :Column1_Update',
    ];
    const SET_BIND_DEFAULT = [
        'Column1_Update' => 'value1',
    ];

    /**
     * @var BindTrait|SetTrait
     */
    private $setTraitClass;

    public function setUp()
    {
        $this->setTraitClass = new class (SetTraitTest::SET_BIND_DEFAULT, SetTraitTest::SET_DEFAULT)
        {
            use BindTrait;
            use SetTrait;

            /**
             * @param array $bindDataDefault
             * @param array $setDataDefault
             */
            public function __construct(array $bindDataDefault, array $setDataDefault)
            {
                $this->bind = $bindDataDefault;
                $this->set = $setDataDefault;
            }

            /**
             * @return array
             */
            public function setData(): array
            {
                return $this->set;
            }

            public function clearSetData()
            {
                $this->set = [];
            }

            public function buildSetQueryPartPublic(): ?string
            {
                return $this->buildSetQueryPart();
            }
        };
    }

    /**
     * @dataProvider setData
     *
     * @param string $column
     * @param string|int|double|null $value
     */
    public function testSet($column, $value)
    {
        $object = $this->setTraitClass->set($column, $value);
        $this->assertObjectUsesTrait(BindTrait::class, $object);
        $this->assertObjectUsesTrait(SetTrait::class, $object);
        $this->assertEquals(
            \array_merge(
                static::SET_DEFAULT,
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
            $this->setTraitClass->setData()
        );
        $this->assertEquals(
            \array_merge(
                static::SET_BIND_DEFAULT,
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
            $this->setTraitClass->bindData()
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
        $this->setTraitClass->set($column, $value);

        $this->assertEquals(
            \sprintf('%s %s', ConditionEnum::SET, \implode(', ', \array_unique($this->setTraitClass->setData()))),
            $this->setTraitClass->buildSetQueryPartPublic()
        );
    }

    public function testBuildSetQueryPartWhenEmpty()
    {
        $this->setTraitClass->clearSetData();

        $this->assertEquals(null, $this->setTraitClass->buildSetQueryPartPublic());
    }

    /**
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
