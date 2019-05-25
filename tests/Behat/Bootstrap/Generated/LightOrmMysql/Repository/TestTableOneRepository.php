<?php

namespace Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository;

use Janisbiz\LightOrm\Dms\MySQL\Enum\KeywordEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderInterface;
use Janisbiz\LightOrm\Dms\MySQL\Repository\AbstractRepository;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Entity\TestTableOneEntity;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Entity\TestTableOneTwoEntity;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Entity\TestTableTwoEntity;

class TestTableOneRepository extends AbstractRepository
{
    /**
     * @param int $id
     * @param int $intColNotNull
     * @param string $varcharColNotNull
     * @param float $floatColNotNull
     * @param null|int $intColNull
     * @param null|string $varcharColNull
     * @param null|float $floatColNull
     * @param null|string $createdAt
     * @param null|string $updatedAt
     *
     * @return TestTableOneEntity
     */
    public function create(
        $id,
        $intColNotNull,
        $varcharColNotNull,
        $floatColNotNull,
        $intColNull = null,
        $varcharColNull = null,
        $floatColNull = null,
        $createdAt = null,
        $updatedAt = null
    ) {
        $testTableOneEntity = (new TestTableOneEntity())
            ->setId($id)
            ->setIntColNotNull($intColNotNull)
            ->setVarcharColNotNull($varcharColNotNull)
            ->setFloatColNotNull($floatColNotNull)
            ->setIntColNull($intColNull)
            ->setVarcharColNull($varcharColNull)
            ->setFloatColNull($floatColNull)
            ->setCreatedAt($createdAt)
            ->setUpdatedAt($updatedAt)
        ;

        $this
            ->createQueryBuilder($testTableOneEntity)
            ->insert()
        ;

        return $testTableOneEntity;
    }

    /**
     * @param int $id
     * @param int $intColNotNull
     * @param string $varcharColNotNull
     * @param float $floatColNotNull
     * @param null|int $intColNull
     * @param null|string $varcharColNull
     * @param null|float $floatColNull
     * @param null|string $createdAt
     * @param null|string $updatedAt
     *
     * @return TestTableOneEntity
     */
    public function createOnDuplicateKeyUpdate(
        $id,
        $intColNotNull,
        $varcharColNotNull,
        $floatColNotNull,
        $intColNull = null,
        $varcharColNull = null,
        $floatColNull = null,
        $createdAt = null,
        $updatedAt = null
    ) {
        $testTableOneEntity = (new TestTableOneEntity())
            ->setId($id)
            ->setIntColNotNull($intColNotNull)
            ->setVarcharColNotNull($varcharColNotNull)
            ->setFloatColNotNull($floatColNotNull)
            ->setIntColNull($intColNull)
            ->setVarcharColNull($varcharColNull)
            ->setFloatColNull($floatColNull)
            ->setCreatedAt($createdAt)
            ->setUpdatedAt($updatedAt)
        ;

        $this
            ->createQueryBuilder($testTableOneEntity)
            ->onDuplicateKeyUpdate(TestTableOneEntity::COLUMN_UPDATED_AT, $updatedAt)
            ->insert()
        ;

        return $testTableOneEntity;
    }

    /**
     * @param int $id
     * @param int $intColNotNull
     * @param string $varcharColNotNull
     * @param float $floatColNotNull
     * @param null|int $intColNull
     * @param null|string $varcharColNull
     * @param null|float $floatColNull
     * @param null|string $createdAt
     * @param null|string $updatedAt
     *
     * @return TestTableOneEntity
     */
    public function createIgnore(
        $id,
        $intColNotNull,
        $varcharColNotNull,
        $floatColNotNull,
        $intColNull = null,
        $varcharColNull = null,
        $floatColNull = null,
        $createdAt = null,
        $updatedAt = null
    ) {
        $testTableOneEntity = (new TestTableOneEntity())
            ->setId($id)
            ->setIntColNotNull($intColNotNull)
            ->setVarcharColNotNull($varcharColNotNull)
            ->setFloatColNotNull($floatColNotNull)
            ->setIntColNull($intColNull)
            ->setVarcharColNull($varcharColNull)
            ->setFloatColNull($floatColNull)
            ->setCreatedAt($createdAt)
            ->setUpdatedAt($updatedAt)
        ;

        $this
            ->createQueryBuilder($testTableOneEntity)
            ->insertIgnore()
        ;

        return $testTableOneEntity;
    }

    /**
     * @param int $id
     * @param int $intColNotNull
     * @param string $varcharColNotNull
     * @param float $floatColNotNull
     * @param null|int $intColNull
     * @param null|string $varcharColNull
     * @param null|float $floatColNull
     * @param null|string $createdAt
     * @param null|string $updatedAt
     *
     * @return TestTableOneEntity
     */
    public function createReplace(
        $id,
        $intColNotNull,
        $varcharColNotNull,
        $floatColNotNull,
        $intColNull = null,
        $varcharColNull = null,
        $floatColNull = null,
        $createdAt = null,
        $updatedAt = null
    ) {
        $testTableOneEntity = (new TestTableOneEntity())
            ->setId($id)
            ->setIntColNotNull($intColNotNull)
            ->setVarcharColNotNull($varcharColNotNull)
            ->setFloatColNotNull($floatColNotNull)
            ->setIntColNull($intColNull)
            ->setVarcharColNull($varcharColNull)
            ->setFloatColNull($floatColNull)
            ->setCreatedAt($createdAt)
            ->setUpdatedAt($updatedAt)
        ;

        $this
            ->createQueryBuilder($testTableOneEntity)
            ->replace()
        ;

        return $testTableOneEntity;
    }

    /**
     * @return TestTableOneEntity[]
     */
    public function read()
    {
        return $this->createQueryBuilder()->find();
    }

    /**
     * @return TestTableOneEntity[]
     */
    public function readWithAllQueryParts()
    {
        return $this->readWithAllQueryPartsQuery()->find();
    }

    /**
     * @return int
     */
    public function readCount()
    {
        return $this->createQueryBuilder()->count();
    }

    /**
     * @return int
     */
    public function readCountWithAllQueryParts()
    {
        return $this->readWithAllQueryPartsQuery()->count();
    }

    /**
     * @param int $id
     * @param int $intColNotNull
     * @param string $varcharColNotNull
     * @param float $floatColNotNull
     * @param null|int $intColNull
     * @param null|string $varcharColNull
     * @param null|float $floatColNull
     * @param null|string $createdAt
     * @param null|string $updatedAt
     *
     * @return TestTableOneEntity
     */
    public function updateRow(
        $id,
        $varcharColNotNull,
        $varcharColNull = null,
        $updatedAt = null
    ) {
        $testTableOneEntity = $this
            ->createQueryBuilder()
            ->where(
                'test_table_one.id = :id',
                [
                    'id' => $id,
                ]
            )
            ->findOne()
        ;

        $testTableOneEntity
            ->setVarcharColNotNull($varcharColNotNull)
            ->setVarcharColNull($varcharColNull)
            ->setUpdatedAt($updatedAt)
        ;

        return $this->createQueryBuilder($testTableOneEntity)->update();
    }

    /**
     * @param int $id
     */
    public function deleteRow($id)
    {
        $testTableOneEntity = $this
            ->createQueryBuilder()
            ->where(
                'test_table_one.id = :id',
                [
                    'id' => $id,
                ]
            )
            ->findOne()
        ;

        $this->createQueryBuilder($testTableOneEntity)->delete();
    }

    /**
     * @param TestTableOneEntity $testTableOneEntity
     */
    public function deleteEntity(TestTableOneEntity $testTableOneEntity)
    {
        $this->createQueryBuilder($testTableOneEntity)->delete();
    }

    /**
     * @return QueryBuilderInterface
     */
    private function readWithAllQueryPartsQuery()
    {
        return $this
            ->createQueryBuilder()
            ->column('test_table_two.id AS test_table_two_id')
            ->innerJoin(TestTableOneTwoEntity::TABLE_NAME, 'test_table_one_two.test_table_one_id = test_table_one.id')
            ->innerJoin(TestTableTwoEntity::TABLE_NAME, 'test_table_two.id = test_table_one_two.test_table_two_id')
            ->where('test_table_one.id != :id', ['id' => 1])
            ->whereIn(
                'test_table_one.id',
                [
                    2,
                    3,
                    4,
                    5
                ]
            )
            ->whereNotIn(
                'test_table_one.id',
                [
                    6,
                    7,
                    8,
                    9,
                    10
                ]
            )
            ->groupBy('test_table_one.id')
            ->having('test_table_one.id != :havingId', ['havingId' => 3])
            ->orderBy('test_table_one.id', KeywordEnum::ASC)
            ->limit(1)
            ->offset(1)
        ;
    }

    /**
    * @return string
    */
    protected function getModelClass()
    {
        return TestTableOneEntity::class;
    }
}
