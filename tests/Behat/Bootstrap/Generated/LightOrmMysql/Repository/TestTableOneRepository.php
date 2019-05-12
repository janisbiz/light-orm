<?php

namespace Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository;

use Janisbiz\LightOrm\Dms\MySQL\Repository\AbstractRepository;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Entity\TestTableOneEntity;

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
    * @return string
    */
    protected function getModelClass()
    {
        return TestTableOneEntity::class;
    }
}
