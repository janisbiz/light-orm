<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository;

use Janisbiz\LightOrm\Dms\MySQL\Repository\AbstractRepository;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Entity\TestTableTwoEntity;

class TestTableTwoRepository extends AbstractRepository
{
    public function create(?int $id): TestTableTwoEntity
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
    public function read(): array
    {
        return $this->createQueryBuilder()->find();
    }

    public function readCount(): int
    {
        return $this->createQueryBuilder()->count();
    }

    public function deleteEntity(TestTableTwoEntity $testTableTwoEntity)
    {
        $this->createQueryBuilder($testTableTwoEntity)->delete();
    }

    protected function getEntityClass(): string
    {
        return TestTableTwoEntity::class;
    }
}
