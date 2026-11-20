<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository;

use Janisbiz\LightOrm\Dms\MySQL\Enum\KeywordEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderInterface;
use Janisbiz\LightOrm\Dms\MySQL\Repository\AbstractRepository;
use Janisbiz\LightOrm\Paginator\PaginatorInterface;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Entity\TestTableOneEntity;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Entity\TestTableOneTwoEntity;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Entity\TestTableTwoEntity;

class TestTableOneRepository extends AbstractRepository
{
    public function create(
        ?int $id,
        int $intColNotNull,
        string $varcharColNotNull,
        float $floatColNotNull,
        ?int $intColNull = null,
        ?string $varcharColNull = null,
        ?float $floatColNull = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ): TestTableOneEntity {
        $testTableOneEntity = (new TestTableOneEntity())
            ->setId($id)
            ->setIntColNotNull($intColNotNull)
            ->setVarcharColNotNull($varcharColNotNull)
            ->setFloatColNotNull($floatColNotNull)
            ->setIntColNull($intColNull)
            ->setVarcharColNull($varcharColNull)
            ->setFloatColNull($floatColNull)
            ->setCreatedAt($createdAt)
            ->setUpdatedAt($updatedAt)
        ;

        $this
            ->createQueryBuilder($testTableOneEntity)
            ->insert()
        ;

        return $testTableOneEntity;
    }

    public function createOnDuplicateKeyUpdate(
        ?int $id,
        int $intColNotNull,
        string $varcharColNotNull,
        float $floatColNotNull,
        ?int $intColNull = null,
        ?string $varcharColNull = null,
        ?float $floatColNull = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ): TestTableOneEntity {
        $testTableOneEntity = (new TestTableOneEntity())
            ->setId($id)
            ->setIntColNotNull($intColNotNull)
            ->setVarcharColNotNull($varcharColNotNull)
            ->setFloatColNotNull($floatColNotNull)
            ->setIntColNull($intColNull)
            ->setVarcharColNull($varcharColNull)
            ->setFloatColNull($floatColNull)
            ->setCreatedAt($createdAt)
            ->setUpdatedAt($updatedAt)
        ;

        $this
            ->createQueryBuilder($testTableOneEntity)
            ->onDuplicateKeyUpdate(TestTableOneEntity::COLUMN_UPDATED_AT, $updatedAt)
            ->insert()
        ;

        return $testTableOneEntity;
    }

    public function createIgnore(
        ?int $id,
        int $intColNotNull,
        string $varcharColNotNull,
        float $floatColNotNull,
        ?int $intColNull = null,
        ?string $varcharColNull = null,
        ?float $floatColNull = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ): TestTableOneEntity {
        $testTableOneEntity = (new TestTableOneEntity())
            ->setId($id)
            ->setIntColNotNull($intColNotNull)
            ->setVarcharColNotNull($varcharColNotNull)
            ->setFloatColNotNull($floatColNotNull)
            ->setIntColNull($intColNull)
            ->setVarcharColNull($varcharColNull)
            ->setFloatColNull($floatColNull)
            ->setCreatedAt($createdAt)
            ->setUpdatedAt($updatedAt)
        ;

        $this
            ->createQueryBuilder($testTableOneEntity)
            ->insertIgnore()
        ;

        return $testTableOneEntity;
    }

    public function createReplace(
        ?int $id,
        int $intColNotNull,
        string $varcharColNotNull,
        float $floatColNotNull,
        ?int $intColNull = null,
        ?string $varcharColNull = null,
        ?float $floatColNull = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ): TestTableOneEntity {
        $testTableOneEntity = (new TestTableOneEntity())
            ->setId($id)
            ->setIntColNotNull($intColNotNull)
            ->setVarcharColNotNull($varcharColNotNull)
            ->setFloatColNotNull($floatColNotNull)
            ->setIntColNull($intColNull)
            ->setVarcharColNull($varcharColNull)
            ->setFloatColNull($floatColNull)
            ->setCreatedAt($createdAt)
            ->setUpdatedAt($updatedAt)
        ;

        $this
            ->createQueryBuilder($testTableOneEntity)
            ->replace()
        ;

        return $testTableOneEntity;
    }

    /**
     * @return TestTableOneEntity[]
     */
    public function read(): array
    {
        return $this->createQueryBuilder()->find();
    }

    /**
     * @return TestTableOneEntity[]
     */
    public function readWithAllQueryParts(): array
    {
        return $this->readWithAllQueryPartsQuery()->find();
    }

    public function readCount(): int
    {
        return $this->createQueryBuilder()->count();
    }

    public function readCountWithAllQueryParts(): int
    {
        return $this->readWithAllQueryPartsQuery()->count();
    }

    public function createPaginator(int $pageSize, int $currentPage): PaginatorInterface
    {
        return $this->paginator($this->createQueryBuilder(), (int) $pageSize, (int) $currentPage);
    }

    public function updateRow(
        int $id,
        string $varcharColNotNull,
        ?string $varcharColNull = null,
        ?string $updatedAt = null
    ): TestTableOneEntity {
        $testTableOneEntity = $this
            ->createQueryBuilder()
            ->where(
                'test_table_one.id = :id',
                [
                    'id' => $id,
                ]
            )
            ->findOne()
        ;

        $testTableOneEntity
            ->setVarcharColNotNull($varcharColNotNull)
            ->setVarcharColNull($varcharColNull)
            ->setUpdatedAt($updatedAt)
        ;

        return $this->createQueryBuilder($testTableOneEntity)->update();
    }

    public function deleteRow(int $id)
    {
        $testTableOneEntity = $this
            ->createQueryBuilder()
            ->where(
                'test_table_one.id = :id',
                [
                    'id' => $id,
                ]
            )
            ->findOne()
        ;

        $this->createQueryBuilder($testTableOneEntity)->delete();
    }

    public function deleteEntity(TestTableOneEntity $testTableOneEntity)
    {
        $this->createQueryBuilder($testTableOneEntity)->delete();
    }

    private function readWithAllQueryPartsQuery(): QueryBuilderInterface
    {
        return $this
            ->createQueryBuilder()
            ->column('test_table_two.id AS test_table_two_id')
            ->innerJoin(TestTableOneTwoEntity::TABLE_NAME, 'test_table_one_two.test_table_one_id = test_table_one.id')
            ->innerJoin(TestTableTwoEntity::TABLE_NAME, 'test_table_two.id = test_table_one_two.test_table_two_id')
            ->where('test_table_one.id != :id', ['id' => 1])
            ->whereIn(
                'test_table_one.id',
                [
                    2,
                    3,
                    4,
                    5
                ]
            )
            ->whereNotIn(
                'test_table_one.id',
                [
                    6,
                    7,
                    8,
                    9,
                    10
                ]
            )
            ->groupBy('test_table_one.id')
            ->having('test_table_one.id != :havingId', ['havingId' => 3])
            ->orderBy('test_table_one.id', KeywordEnum::ASC)
            ->limit(1)
            ->offset(1)
        ;
    }

    protected function getEntityClass(): string
    {
        return TestTableOneEntity::class;
    }
}
