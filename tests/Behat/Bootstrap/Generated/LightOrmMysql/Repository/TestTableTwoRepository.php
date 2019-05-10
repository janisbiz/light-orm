<?php

namespace Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository;

use Janisbiz\LightOrm\Dms\MySQL\Repository\AbstractRepository;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Entity\TestTableTwoEntity;

class TestTableTwoRepository extends AbstractRepository
{
    /**
     * @param int $id
     */
    public function create($id) {
        $this
            ->createQueryBuilder((new TestTableTwoEntity())->setId($id))
            ->onDuplicateKeyUpdate(TestTableTwoEntity::COLUMN_ID, $id)
            ->insert()
        ;
    }

    /**
    * @return string
    */
    protected function getModelClass()
    {
        return TestTableTwoEntity::class;
    }
}
