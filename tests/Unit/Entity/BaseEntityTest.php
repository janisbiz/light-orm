<?php

namespace Janisbiz\LightOrm\Tests\Unit\Entity;

use Janisbiz\LightOrm\Entity\BaseEntity;
use PHPUnit\Framework\TestCase;

class BaseEntityTest extends TestCase
{
    const COL_ONE_VALUE = 'valOne';
    const COL_ONE_VALUE_REPLACEMENT = 'valOneReplacement';
    const COL_TWO_VALUE = 2;
    const COL_TWO_VALUE_REPLACEMENT = 2.2;
    const COL_THREE_VALUE = 3.3;
    const COL_THREE_VALUE_REPLACEMENT = 3.33;
    const COL_FOUR_VALUE = 'This is column four!';
    const COL_FIVE_VALUE = 'This is column five!';

    /**
     * @var BaseEntity
     */
    private $baseEntity;

    public function setUp()
    {
        $baseEntity = new BaseEntity();

        $baseEntity->col_one = self::COL_ONE_VALUE;
        $baseEntity->col_two = self::COL_TWO_VALUE;
        $baseEntity->col_three = self::COL_THREE_VALUE;

        $this->baseEntity = $baseEntity;
    }

    public function testGetters()
    {
        $this->assertEquals(self::COL_ONE_VALUE, $this->baseEntity->getColOne());
        $this->assertEquals(self::COL_ONE_VALUE, $this->baseEntity->col_one);
        $this->assertEquals(self::COL_TWO_VALUE, $this->baseEntity->getColTwo());
        $this->assertEquals(self::COL_TWO_VALUE, $this->baseEntity->col_two);
        $this->assertEquals(self::COL_THREE_VALUE, $this->baseEntity->getColThree());
        $this->assertEquals(self::COL_THREE_VALUE, $this->baseEntity->col_three);
    }

    /**
     * @codeCoverageIgnore
     * @expectedException \Exception
     * @expectedExceptionMessage Call to undefined method Janisbiz\LightOrm\Entity\BaseEntity::getInvalidCol()
     */
    public function testGettersInvalid()
    {
        $this->baseEntity->getInvalidCol();
    }

    public function testSetters()
    {
        $this->baseEntity->setColThree(self::COL_THREE_VALUE_REPLACEMENT);
        $this->assertEquals(self::COL_THREE_VALUE_REPLACEMENT, $this->baseEntity->getColThree());
        $this->assertEquals(self::COL_THREE_VALUE_REPLACEMENT, $this->baseEntity->col_three);

        $this->baseEntity->setColFour(self::COL_FOUR_VALUE);
        $this->assertEquals(self::COL_FOUR_VALUE, $this->baseEntity->getColFour());
        $this->assertEquals(self::COL_FOUR_VALUE, $this->baseEntity->col_four);

        $this->baseEntity->col_one = self::COL_ONE_VALUE_REPLACEMENT;
        $this->assertEquals(self::COL_ONE_VALUE_REPLACEMENT, $this->baseEntity->getColOne());
        $this->assertEquals(self::COL_ONE_VALUE_REPLACEMENT, $this->baseEntity->col_one);

        $this->baseEntity->col_five = self::COL_FIVE_VALUE;
        $this->assertEquals(self::COL_FIVE_VALUE, $this->baseEntity->getColFive());
        $this->assertEquals(self::COL_FIVE_VALUE, $this->baseEntity->col_five);
    }

    public function testData()
    {
        $data = &$this->baseEntity->data();

        $this->assertCount(3, $data);
        $this->baseEntity->setColFour(self::COL_FOUR_VALUE);
        $this->assertCount(4, $data);
        $data['col_five'] = self::COL_FIVE_VALUE;
        $this->assertCount(5, $data);
        $this->assertEquals(self::COL_FIVE_VALUE, $this->baseEntity->getColFive());
        $this->assertEquals(self::COL_FIVE_VALUE, $this->baseEntity->col_five);
        $this->assertEquals(self::COL_FIVE_VALUE, $this->baseEntity->data('col_five'));
    }

    /**
     * @codeCoverageIgnore
     * @expectedException \Exception
     * @expectedExceptionMessage There is no key "ivalid_key" present in data!
     */
    public function testDataWithInvalidKey()
    {
        $this->baseEntity->data('ivalid_key');
    }

    public function testDataOriginal()
    {
        $data = &$this->baseEntity->data();
        $dataOriginal = &$this->baseEntity->dataOriginal();

        $this->assertCount(3, $dataOriginal);

        $this->baseEntity->setColFour(self::COL_FOUR_VALUE);
        $this->assertCount(3, $dataOriginal);

        $data['col_five'] = self::COL_FIVE_VALUE;
        $this->assertCount(3, $dataOriginal);

        $this->assertEquals(self::COL_FIVE_VALUE, $this->baseEntity->getColFive());
        $this->assertEquals(self::COL_FIVE_VALUE, $this->baseEntity->dataOriginal('col_five'));
        $this->assertEquals(self::COL_FIVE_VALUE, $this->baseEntity->col_five);

        $this->baseEntity->col_five = self::COL_FIVE_VALUE;
        $this->assertCount(4, $dataOriginal);
    }

    /**
     * @codeCoverageIgnore
     * @expectedException \Exception
     * @expectedExceptionMessage There is no key "ivalid_key" present in data original!
     */
    public function testDataOriginalWithInvalidKey()
    {
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
