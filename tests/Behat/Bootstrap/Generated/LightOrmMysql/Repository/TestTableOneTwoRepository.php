<?php

namespace Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository;

use Janisbiz\LightOrm\Dms\MySQL\Repository\AbstractRepository;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Entity\TestTableOneTwoEntity;

class TestTableOneTwoRepository extends AbstractRepository
{
    /**
     * @param int $testTableOneId
     * @param int $testTableTwoId
     */
    public function create($testTableOneId, $testTableTwoId)
    {
        $this
            ->createQueryBuilder(
                (new TestTableOneTwoEntity())
                    ->setTestTableOneId($testTableOneId)
                    ->setTestTableTwoId($testTableTwoId)
            )
            ->insert()
        ;
    }

    /**
     * @return TestTableOneTwoEntity[]
     */
    public function read()
    {
        return $this->createQueryBuilder()->orderBy(TestTableOneTwoEntity::COLUMN_TEST_TABLE_ONE_ID)->find();
    }

    /**
     * @return int
     */
    public function readCount()
    {
        return $this->createQueryBuilder()->count();
    }

    /**
     * @param int $testTableOneIdCurrent
     * @param int $testTableTwoIdCurrent
     * @param int $testTableOneId
     * @param int $testTableTwoId
     *
     * @return TestTableOneTwoEntity
     */
    public function updateRow(
        $testTableOneIdCurrent,
        $testTableTwoIdCurrent,
        $testTableOneId,
        $testTableTwoId
    ) {
        $testTableOneTwoEntity = $this
            ->createQueryBuilder()
            ->where('test_table_one_two.test_table_one_id = :test_table_one_id')
            ->where('test_table_one_two.test_table_two_id = :test_table_two_id')
            ->bind([
                'test_table_one_id' => $testTableOneIdCurrent,
                'test_table_two_id' => $testTableTwoIdCurrent,
            ])
            ->findOne()
        ;

        $testTableOneTwoEntity
            ->setTestTableOneId($testTableOneId)
            ->setTestTableTwoId($testTableTwoId)
        ;

        return $this->createQueryBuilder($testTableOneTwoEntity)->update();
    }

    /**
     * @param TestTableOneTwoEntity $testTableOneTwoEntity
     * @param int $testTableOneId
     * @param int $testTableTwoId
     *
     * @return TestTableOneTwoEntity
     */
    public function updateEntity(
        TestTableOneTwoEntity $testTableOneTwoEntity,
        $testTableOneId,
        $testTableTwoId
    ) {
        $testTableOneTwoEntity
            ->setTestTableOneId($testTableOneId)
            ->setTestTableTwoId($testTableTwoId)
        ;

        return $this->createQueryBuilder($testTableOneTwoEntity)->update();
    }

    /**
     * @param TestTableOneTwoEntity $testTableOneTwoEntity
     */
    public function deleteEntity(TestTableOneTwoEntity $testTableOneTwoEntity)
    {
        $this->createQueryBuilder($testTableOneTwoEntity)->delete();
    }

    /**
    * @return string
    */
    protected function getModelClass()
    {
        return TestTableOneTwoEntity::class;
    }
}
