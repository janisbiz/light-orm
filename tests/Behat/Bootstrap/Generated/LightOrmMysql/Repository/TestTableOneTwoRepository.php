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
    public function create($testTableOneId, $testTableTwoId) {
        $this
            ->createQueryBuilder(
                (new TestTableOneTwoEntity())
                    ->setTestTableOneId($testTableOneId)
                    ->setTestTableTwoId($testTableTwoId)
            )
            ->onDuplicateKeyUpdate(TestTableOneTwoEntity::COLUMN_TEST_TABLE_ONE_ID, $testTableOneId)
            ->onDuplicateKeyUpdate(TestTableOneTwoEntity::COLUMN_TEST_TABLE_ONE_ID, $testTableTwoId)
            ->insert()
        ;
    }

    /**
    * @return string
    */
    protected function getModelClass()
    {
        return TestTableOneTwoEntity::class;
    }
}
