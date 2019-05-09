<?php

namespace Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository;

use Janisbiz\LightOrm\Dms\MySQL\Repository\AbstractRepository;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Entity\TestTableOneTwoEntity;

class TestTableOneTwoRepository extends AbstractRepository
{
    /**
    * @return string
    */
    protected function getModelClass()
    {
        return TestTableOneTwoEntity::class;
    }
}
