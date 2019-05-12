<?php

namespace Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository;

use Janisbiz\LightOrm\Dms\MySQL\Repository\AbstractRepository;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Entity\TestTableTwoEntity;

class TestTableTwoRepository extends AbstractRepository
{
    /**
     * @param int $id
     *
     * @return TestTableTwoEntity
     */
    public function create($id)
    {
        $testTableTwoEntity = (new TestTableTwoEntity())->setId($id);

        $this
            ->createQueryBuilder($testTableTwoEntity)
            ->insert()
        ;

        return $testTableTwoEntity;
    }

    /**
     * @return TestTableTwoEntity[]
     */
    public function read()
    {
        return $this->createQueryBuilder()->find();
    }

    /**
     * @param TestTableTwoEntity $testTableTwoEntity
     */
    public function deleteEntity(TestTableTwoEntity $testTableTwoEntity)
    {
        $this->createQueryBuilder($testTableTwoEntity)->delete();
    }

    /**
    * @return string
    */
    protected function getModelClass()
    {
        return TestTableTwoEntity::class;
    }
}
