<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository;

use Janisbiz\LightOrm\Dms\MySQL\Repository\AbstractRepository;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Entity\TestTableOneTwoEntity;

class TestTableOneTwoRepository extends AbstractRepository
{
    public function create(int $testTableOneId, int $testTableTwoId)
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
    public function read(): array
    {
        return $this->createQueryBuilder()->orderBy(TestTableOneTwoEntity::COLUMN_TEST_TABLE_ONE_ID)->find();
    }

    public function readCount(): int
    {
        return $this->createQueryBuilder()->count();
    }

    public function updateRow(
        int $testTableOneIdCurrent,
        int $testTableTwoIdCurrent,
        int $testTableOneId,
        int $testTableTwoId
    ): TestTableOneTwoEntity {
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

    public function updateEntity(
        TestTableOneTwoEntity $testTableOneTwoEntity,
        int $testTableOneId,
        int $testTableTwoId
    ): TestTableOneTwoEntity {
        $testTableOneTwoEntity
            ->setTestTableOneId($testTableOneId)
            ->setTestTableTwoId($testTableTwoId)
        ;

        return $this->createQueryBuilder($testTableOneTwoEntity)->update();
    }

    public function deleteEntity(TestTableOneTwoEntity $testTableOneTwoEntity)
    {
        $this->createQueryBuilder($testTableOneTwoEntity)->delete();
    }

    protected function getEntityClass(): string
    {
        return TestTableOneTwoEntity::class;
    }
}
