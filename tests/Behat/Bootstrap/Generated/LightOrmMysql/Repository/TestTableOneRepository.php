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
        $this
            ->createQueryBuilder(
                (new TestTableOneEntity())
                    ->setId($id)
                    ->setIntColNotNull($intColNotNull)
                    ->setVarcharColNotNull($varcharColNotNull)
                    ->setFloatColNotNull($floatColNotNull)
                    ->setIntColNull($intColNull)
                    ->setVarcharColNull($varcharColNull)
                    ->setFloatColNull($floatColNull)
                    ->setCreatedAt($createdAt)
                    ->setUpdatedAt($updatedAt)
            )
            ->insert()
        ;
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
        $this
            ->createQueryBuilder(
                (new TestTableOneEntity())
                    ->setId($id)
                    ->setIntColNotNull($intColNotNull)
                    ->setVarcharColNotNull($varcharColNotNull)
                    ->setFloatColNotNull($floatColNotNull)
                    ->setIntColNull($intColNull)
                    ->setVarcharColNull($varcharColNull)
                    ->setFloatColNull($floatColNull)
                    ->setCreatedAt($createdAt)
                    ->setUpdatedAt($updatedAt)
            )
            ->onDuplicateKeyUpdate(TestTableOneEntity::COLUMN_UPDATED_AT, $updatedAt)
            ->insert()
        ;
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
        $this
            ->createQueryBuilder(
                (new TestTableOneEntity())
                    ->setId($id)
                    ->setIntColNotNull($intColNotNull)
                    ->setVarcharColNotNull($varcharColNotNull)
                    ->setFloatColNotNull($floatColNotNull)
                    ->setIntColNull($intColNull)
                    ->setVarcharColNull($varcharColNull)
                    ->setFloatColNull($floatColNull)
                    ->setCreatedAt($createdAt)
                    ->setUpdatedAt($updatedAt)
            )
            ->insertIgnore()
        ;
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
        $this
            ->createQueryBuilder(
                (new TestTableOneEntity())
                    ->setId($id)
                    ->setIntColNotNull($intColNotNull)
                    ->setVarcharColNotNull($varcharColNotNull)
                    ->setFloatColNotNull($floatColNotNull)
                    ->setIntColNull($intColNull)
                    ->setVarcharColNull($varcharColNull)
                    ->setFloatColNull($floatColNull)
                    ->setCreatedAt($createdAt)
                    ->setUpdatedAt($updatedAt)
            )
            ->replace()
        ;
    }

    /**
     * @return TestTableOneEntity[]
     */
    public function read()
    {
        return $this->createQueryBuilder()->find();
    }

    public function beginTransaction()
    {
        $this->getConnection()->beginTransaction();
    }

    public function commitTransaction()
    {
        $this->getConnection()->commit();
    }

    /**
    * @return string
    */
    protected function getModelClass()
    {
        return TestTableOneEntity::class;
    }
}
