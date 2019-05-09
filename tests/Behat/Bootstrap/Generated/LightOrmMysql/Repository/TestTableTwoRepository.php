<?php

namespace Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository;

use Janisbiz\LightOrm\Dms\MySQL\Repository\AbstractRepository;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Entity\TestTableTwoEntity;

class TestTableTwoRepository extends AbstractRepository
{
    /**
    * @return string
    */
    protected function getModelClass()
    {
        return TestTableTwoEntity::class;
    }
}
