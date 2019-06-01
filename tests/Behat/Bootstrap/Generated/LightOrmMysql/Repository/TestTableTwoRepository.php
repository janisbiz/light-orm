<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository;

use Janisbiz\LightOrm\Dms\MySQL\Repository\AbstractRepository;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Entity\TestTableTwoEntity;

class TestTableTwoRepository extends AbstractRepository
{
    /**
     * @param null|int $id
     *
     * @return TestTableTwoEntity
     */
    public function create(?int $id)
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
     * @return int
     */
    public function readCount()
    {
        return $this->createQueryBuilder()->count();
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
    protected function getModelClass(): string
    {
        return TestTableTwoEntity::class;
    }
}
