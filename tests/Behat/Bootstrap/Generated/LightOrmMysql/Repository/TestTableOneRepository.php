<?php

namespace JanisBiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository;

use Janisbiz\LightOrm\Dms\MySQL\Repository\AbstractRepository;
use JanisBiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Entity\TestTableOneEntity;

class TestTableOneRepository extends AbstractRepository
{
    /**
    * @return string
    */
    protected function getModelClass()
    {
        return TestTableOneEntity::class;
    }
}
