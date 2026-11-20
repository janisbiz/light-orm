<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Unit\Dms\MySQL\QueryBuilder\Traits;

use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderException;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\OnDuplicateKeyUpdateTrait;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\Traits\ValueTrait;
use PHPUnit\Framework\Attributes\DataProvider;

class OnDuplicateKeyUpdateTraitTest extends AbstractTraitTestCase
{
    const array ON_DUPLICATE_KEY_UPDATE_DEFAULT = [
        'column1' => ':Column1_OnDuplicateKeyUpdate',
    ];
    const array ON_DUPLICATE_KEY_UPDATE_BIND_VALUE_DEFAULT = [
        'Column1_OnDuplicateKeyUpdate' => 'value1',
    ];
    const array ON_DUPLICATE_KEY_UPDATE_BIND_VALUE_OVERRIDE = [
        'Column1_OnDuplicateKeyUpdate' => 'value1_override',
    ];
    const array ON_DUPLICATE_KEY_UPDATE_BIND_VALUE = [
        'Column2_OnDuplicateKeyUpdate' => 'value2',
    ];

    /**
     * @var ValueTrait|OnDuplicateKeyUpdateTrait
     */
    private $onDuplicateKeyUpdateTraitClass;
    
    public function setUp(): void
    {
        $this->onDuplicateKeyUpdateTraitClass = new class (
            OnDuplicateKeyUpdateTraitTest::ON_DUPLICATE_KEY_UPDATE_BIND_VALUE_DEFAULT,
            OnDuplicateKeyUpdateTraitTest::ON_DUPLICATE_KEY_UPDATE_DEFAULT
        ) {
            use ValueTrait;
            use OnDuplicateKeyUpdateTrait;

            public function __construct(array $bindValueDataDefault, array $onDuplicateKeyUpdateDataDefault)
            {
                $this->bindValue = $bindValueDataDefault;
                $this->onDuplicateKeyUpdate = $onDuplicateKeyUpdateDataDefault;
            }

            public function onDuplicateKeyUpdateData(): array
            {
                return $this->onDuplicateKeyUpdate;
            }

            public function clearOnDuplicateKeyUpdateData()
            {
                $this->onDuplicateKeyUpdate = [];
            }

            public function buildOnDuplicateKeyUpdateQueryPartPublic(): ?string
            {
                return $this->buildOnDuplicateKeyUpdateQueryPart();
            }
        };
    }

    #[DataProvider('onDuplicateKeyUpdateData')]
    public function testOnDuplicateKeyUpdate(string $column, null|int|string|float $value)
    {
        $object = $this->onDuplicateKeyUpdateTraitClass->onDuplicateKeyUpdate($column, $value);
        $this->assertObjectUsesTrait(ValueTrait::class, $object);
        $this->assertObjectUsesTrait(OnDuplicateKeyUpdateTrait::class, $object);

        $this->assertEquals(
            array_merge(
                static::ON_DUPLICATE_KEY_UPDATE_DEFAULT,
                array_reduce(
                    array_map(
                        fn($column) => [
                            $column => sprintf(
                                '%s = :%s_OnDuplicateKeyUpdate',
                                $column,
                                implode(
                                    '_',
                                    array_map(
                                        fn($columnPart) => mb_convert_case($columnPart, MB_CASE_TITLE),
                                        explode('.', $column)
                                    )
                                )
                            ),
                        ],
                        [
                            $column
                        ]
                    ),
                    array_merge(...),
                    []
                )
            ),
            $this->onDuplicateKeyUpdateTraitClass->onDuplicateKeyUpdateData()
        );
        $this->assertEquals(
            array_merge(
                static::ON_DUPLICATE_KEY_UPDATE_BIND_VALUE_DEFAULT,
                array_reduce(
                    array_map(
                        fn($column, $value) => [
                            sprintf(
                                '%s_OnDuplicateKeyUpdate',
                                implode(
                                    '_',
                                    array_map(
                                        fn($columnPart) => mb_convert_case($columnPart, MB_CASE_TITLE),
                                        explode('.', $column)
                                    )
                                )
                            ) => $value,
                        ],
                        [
                            $column
                        ],
                        [
                            $value
                        ]
                    ),
                    array_merge(...),
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

    #[DataProvider('onDuplicateKeyUpdateData')]
    public function testBuildOnDuplicateKeyUpdateQueryPart(string $column, null|int|string|float $value)
    {
        $this->onDuplicateKeyUpdateTraitClass->onDuplicateKeyUpdate($column, $value);

        $this->assertEquals(
            sprintf(
                'ON DUPLICATE KEY UPDATE %s',
                implode(', ', $this->onDuplicateKeyUpdateTraitClass->onDuplicateKeyUpdateData())
            ),
            $this->onDuplicateKeyUpdateTraitClass->buildOnDuplicateKeyUpdateQueryPartPublic()
        );
    }

    public function testBuildOnDuplicateKeyUpdateQueryPartWhenEmpty()
    {
        $this->onDuplicateKeyUpdateTraitClass->clearOnDuplicateKeyUpdateData();

        $this->assertEquals(null, $this->onDuplicateKeyUpdateTraitClass->buildOnDuplicateKeyUpdateQueryPartPublic());
    }

    public static function onDuplicateKeyUpdateData(): array
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
