<?php

namespace Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository;

use Janisbiz\LightOrm\Dms\MySQL\Repository\AbstractRepository;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Entity\TestTableTwoEntity;

class TestTableTwoRepository extends AbstractRepository
{
    /**
     * @param int $id
     */
    public function create($id)
    {
        $this
            ->createQueryBuilder((new TestTableTwoEntity())->setId($id))
            ->insert()
        ;
    }

    /**
     * @return TestTableTwoEntity[]
     */
    public function read()
    {
        return $this->createQueryBuilder()->find();
    }

    /**
    * @return string
    */
    protected function getModelClass()
    {
        return TestTableTwoEntity::class;
    }
}
