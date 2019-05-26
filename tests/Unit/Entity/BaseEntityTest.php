<?php

namespace Janisbiz\LightOrm\Tests\Unit\Entity;

use Janisbiz\LightOrm\Entity\BaseEntity;
use Janisbiz\LightOrm\Entity\EntityException;
use PHPUnit\Framework\TestCase;

class BaseEntityTest extends TestCase
{
    const COL_ONE_VALUE = '<b>valOne</b>';
    const COL_ONE_VALUE_REPLACEMENT = 'valOneReplacement';
    const COL_TWO_VALUE = 2;
    const COL_TWO_VALUE_REPLACEMENT = 2.2;
    const COL_THREE_VALUE = 3.3;
    const COL_THREE_VALUE_REPLACEMENT = 3.33;
    const COL_THREE_VALUE_REPLACEMENT_EMPTY = '';
    const COL_FOUR_VALUE = 'This is column four!';
    const COL_FIVE_VALUE = 'This is column five!';
    const COL_NON_EXISTENT = 'non_existent';
    const COL_NON_EXISTENT_VALUE = null;

    /**
     * @var BaseEntity
     */
    private $baseEntity;

    public function setUp()
    {
        $baseEntity = new BaseEntity();

        $baseEntity->col_one = static::COL_ONE_VALUE;
        $baseEntity->col_two = static::COL_TWO_VALUE;
        $baseEntity->col_three = static::COL_THREE_VALUE;

        $this->baseEntity = $baseEntity;
    }

    public function testGetters()
    {
        $this->assertEquals(static::COL_ONE_VALUE, $this->baseEntity->getColOne());
        $this->assertEquals(
            \nl2br(\htmlspecialchars(\trim(static::COL_ONE_VALUE), ENT_QUOTES, 'UTF-8')),
            $this->baseEntity->getColOne(true)
        );
        $this->assertEquals(static::COL_ONE_VALUE, $this->baseEntity->col_one);
        $this->assertEquals(static::COL_TWO_VALUE, $this->baseEntity->getColTwo());
        $this->assertEquals(static::COL_TWO_VALUE, $this->baseEntity->getColTwo(true));
        $this->assertEquals(static::COL_TWO_VALUE, $this->baseEntity->col_two);
        $this->assertEquals(static::COL_THREE_VALUE, $this->baseEntity->getColThree());
        $this->assertEquals(static::COL_THREE_VALUE, $this->baseEntity->getColThree(true));
        $this->assertEquals(static::COL_THREE_VALUE, $this->baseEntity->col_three);
        $this->assertEquals(static::COL_THREE_VALUE, $this->baseEntity->col_three);
        $this->assertEquals(static::COL_NON_EXISTENT_VALUE, $this->baseEntity->invalid_col);
    }

    public function testGettersInvalid()
    {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage('Call to undefined method Janisbiz\LightOrm\Entity\BaseEntity::getInvalidCol()');

        $this->baseEntity->getInvalidCol();
    }

    public function testSetters()
    {
        $this->baseEntity->setColThree(static::COL_THREE_VALUE_REPLACEMENT);
        $this->assertEquals(static::COL_THREE_VALUE_REPLACEMENT, $this->baseEntity->getColThree());
        $this->assertEquals(static::COL_THREE_VALUE_REPLACEMENT, $this->baseEntity->col_three);

        $this->baseEntity->setColThree(static::COL_THREE_VALUE_REPLACEMENT_EMPTY);
        $this->assertEquals(null, $this->baseEntity->getColThree());
        $this->assertEquals(null, $this->baseEntity->col_three);

        $this->baseEntity->setColFour(static::COL_FOUR_VALUE);
        $this->assertEquals(static::COL_FOUR_VALUE, $this->baseEntity->getColFour());
        $this->assertEquals(static::COL_FOUR_VALUE, $this->baseEntity->col_four);

        $this->baseEntity->col_one = static::COL_ONE_VALUE_REPLACEMENT;
        $this->assertEquals(static::COL_ONE_VALUE_REPLACEMENT, $this->baseEntity->getColOne());
        $this->assertEquals(static::COL_ONE_VALUE_REPLACEMENT, $this->baseEntity->col_one);

        $this->baseEntity->col_five = static::COL_FIVE_VALUE;
        $this->assertEquals(static::COL_FIVE_VALUE, $this->baseEntity->getColFive());
        $this->assertEquals(static::COL_FIVE_VALUE, $this->baseEntity->col_five);
    }

    public function testData()
    {
        $data = &$this->baseEntity->data();

        $this->assertCount(3, $data);
        $this->baseEntity->setColFour(static::COL_FOUR_VALUE);
        $this->assertCount(4, $data);
        $data['col_five'] = static::COL_FIVE_VALUE;
        $this->assertCount(5, $data);
        $this->assertEquals(static::COL_FIVE_VALUE, $this->baseEntity->getColFive());
        $this->assertEquals(static::COL_FIVE_VALUE, $this->baseEntity->col_five);
        $this->assertEquals(static::COL_FIVE_VALUE, $this->baseEntity->data('col_five'));
    }

    public function testDataWithInvalidKey()
    {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage('There is no key "ivalid_key" present in data!');

        $this->baseEntity->data('ivalid_key');
    }

    public function testDataOriginal()
    {
        $data = &$this->baseEntity->data();
        $dataOriginal = &$this->baseEntity->dataOriginal();

        $this->assertCount(3, $dataOriginal);

        $this->baseEntity->setColFour(static::COL_FOUR_VALUE);
        $this->assertCount(3, $dataOriginal);

        $data['col_five'] = static::COL_FIVE_VALUE;
        $this->assertCount(3, $dataOriginal);

        $this->assertEquals(static::COL_FIVE_VALUE, $this->baseEntity->getColFive());
        $this->assertEquals(static::COL_FIVE_VALUE, $this->baseEntity->dataOriginal('col_five'));
        $this->assertEquals(static::COL_FIVE_VALUE, $this->baseEntity->col_five);

        $this->baseEntity->col_five = static::COL_FIVE_VALUE;
        $this->assertCount(4, $dataOriginal);
    }

    public function testDataOriginalWithInvalidKey()
    {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage('There is no key "ivalid_key" present in data original!');

        $this->baseEntity->dataOriginal('ivalid_key');
    }

    public function testIsNew()
    {
        $this->assertTrue($this->baseEntity->isNew());
    }

    public function testIsSaved()
    {
        $this->assertFalse($this->baseEntity->isSaved());
    }

    public function testPrimaryKeys()
    {
        $this->assertCount(0, $this->baseEntity->primaryKeys());
    }

    public function testPrimaryKeysAutoIncrement()
    {
        $this->assertCount(0, $this->baseEntity->primaryKeysAutoIncrement());
    }

    public function testColumns()
    {
        $this->assertCount(0, $this->baseEntity->columns());
    }
}
