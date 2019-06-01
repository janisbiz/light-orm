# Light ORM

[![Latest Stable Version](https://poser.pugx.org/janisbiz/light-orm/v/stable)](https://packagist.org/packages/janisbiz/light-orm)
[![codecov](https://codecov.io/gh/janisbiz/light-orm/branch/master/graph/badge.svg?token=Pzt0e1RZLK)](https://codecov.io/gh/janisbiz/light-orm)
[![Build Status](https://travis-ci.com/janisbiz/light-orm.svg?token=FFYBA1tvzN9FThzx1Ca7&branch=master)](https://travis-ci.com/janisbiz/light-orm)
[![Total Downloads](https://poser.pugx.org/janisbiz/light-orm/downloads)](https://packagist.org/packages/janisbiz/light-orm)
![Deps](https://img.shields.io/badge/dependencies-up%20to%20date-brightgreen.svg)

A light ORM for php with easy to use query builder, repository generator and connection pool manager.

## Installing

`composer require janisbiz/light-orm`

## About

light-orm has been used for a while in some of my work related projects. As it was growing, I decided to open-source it for the rest of community. It has good unit test coverage and a great support for integration testing together with desired
DMS using Behat.

Currently these DMS are supported:
 - MySQL
 
There is still a lot work to do, so it would be great to have some contributors for future improvements

## Examples

### Connection pool

**Connection pool** is a singleton (yes I know...), which holds all the DMS connections used by ORM. To set-up connection pool you need to create a config for your DMS. Afterwards, you can add this config to connection pool, and it will establish connection to server only when it is required. To set-up connection pool, see example below:
```php
<?php

use Janisbiz\LightOrm\Dms\MySQL\Connection\ConnectionConfigUrl as MySQLConnectionConfigUrl;

$databaseName = 'light_orm_mysql';

$mysqlConnectionConfig = new MySQLConnectionConfigUrl(
    \sprintf('mysql://root:password@mysql/%s', $databaseName)
);

$connectionPool = (new ConnectionPool())->addConnectionConfig($mysqlConnectionConfig);
```


### Generator

**Generator** is built in reposintory and entity class generator. With generator, it is possible to make easy use of the ORM. To use generator, you need to have pre-configured connection pool or one connection. To configure generator, see example below:
```php
<?php

$directoryPersistent = \implode(
    '',
    [
        __DIR__,
        DIRECTORY_SEPARATOR,
        '..',
        DIRECTORY_SEPARATOR,
        'tests',
        DIRECTORY_SEPARATOR,
        'Behat',
        DIRECTORY_SEPARATOR,
        'Bootstrap',
        DIRECTORY_SEPARATOR,
        'Generated'
    ]
);
$directoryNonPersistent = \implode(
    '',
    [
        __DIR__,
        DIRECTORY_SEPARATOR,
        '..',
        DIRECTORY_SEPARATOR,
        'var',
        DIRECTORY_SEPARATOR,
        'light-orm',
        DIRECTORY_SEPARATOR,
        'Generated'
    ]
);

$namespacePersistent = 'Janisbiz\LightOrm\Tests\Behat\Generated';
$namespaceNonPersistent = 'Janisbiz\LightOrm\Variable\Generated';

$baseEntityClassWriter = new BaseEntityClassWriter(new WriterConfig(
    $directoryNonPersistent,
    $namespaceNonPersistent,
    'Base'
));
$entityClassWriter = new EntityClassWriter(
    new WriterConfig(
        $directoryPersistent,
        $namespacePersistent,
        '',
        'Entity'
    ),
    $baseEntityClassWriter
);
$repositoryClassWriter = new RepositoryClassWriter(
    new WriterConfig(
        $directoryPersistent,
        $namespacePersistent,
        '',
        'Repository'
    ),
    $entityClassWriter
);

(new Generator(new DmsFactory()))
    ->addWriter($baseEntityClassWriter)
    ->addWriter($entityClassWriter)
    ->addWriter($repositoryClassWriter)
    ->generate($connectionPool->getConnection($databaseName), $databaseName)
;
```

### Repository & Query Builder

Repository is place where you can call query builder and then execute it against your configured DMS. Repository supports all basic CRUD actions against DMS, as well, it has built in paginator for result set pagination and result set count. To use query builder on repository, please see example below:
```
<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository;

use Janisbiz\LightOrm\Dms\MySQL\Enum\KeywordEnum;
use Janisbiz\LightOrm\Dms\MySQL\QueryBuilder\QueryBuilderInterface;
use Janisbiz\LightOrm\Dms\MySQL\Repository\AbstractRepository;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Entity\TestTableOneEntity;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Entity\TestTableOneTwoEntity;
use Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Entity\TestTableTwoEntity;

class TestTableOneRepository extends AbstractRepository
{
    /**
     * @param null|int $id
     * @param int $intColNotNull
     * @param string $varcharColNotNull
     * @param float $floatColNotNull
     * @param null|int $intColNull
     * @param null|string $varcharColNull
     * @param null|float $floatColNull
     * @param null|string $createdAt
     * @param null|string $updatedAt
     *
     * @return TestTableOneEntity
     */
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
    ) {
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

    /**
     * @return TestTableOneEntity[]
     */
    public function readWithAllQueryParts()
    {
        return $this->readWithAllQueryPartsQuery()->find();
    }

    /**
     * @return int
     */
    public function readCountWithAllQueryParts()
    {
        return $this->readWithAllQueryPartsQuery()->count();
    }

    /**
     * @param int $pageSize
     * @param int $currentPage
     *
     * @return TestTableOneEntity[]
     */
    public function createPaginator($pageSize, $currentPage)
    {
        return $this->paginator($this->createQueryBuilder(), (int) $pageSize, (int) $currentPage);
    }

    /**
     * @param int $id
     * @param int $intColNotNull
     * @param string $varcharColNotNull
     * @param float $floatColNotNull
     * @param null|int $intColNull
     * @param null|string $varcharColNull
     * @param null|float $floatColNull
     * @param null|string $createdAt
     * @param null|string $updatedAt
     *
     * @return TestTableOneEntity
     */
    public function updateRow(
        $id,
        $varcharColNotNull,
        $varcharColNull = null,
        $updatedAt = null
    ) {
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

    /**
     * @param TestTableOneEntity $testTableOneEntity
     */
    public function deleteEntity(TestTableOneEntity $testTableOneEntity)
    {
        $this->createQueryBuilder($testTableOneEntity)->delete();
    }

    /**
     * @return QueryBuilderInterface
     */
    private function readWithAllQueryPartsQuery()
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

    /**
    * @return string
    */
    protected function getModelClass(): string
    {
        return TestTableOneEntity::class;
    }
}
```
### More examples?

To see full power of light-orm, please see test cases:
- For connection handling, see [Connection Feature](tests/Behat/Features/Connection)
- For generaotr handling(MySQL), see [MySQL Generator Feature](tests/Behat/Features/Dms/MySQL/Generator)
- For repository handling(MySQL), see [MySQL Repository Feature](tests/Behat/Features/Dms/MySQL/Repository) and [generated repositories with respective entity classes](tests/Behat/Bootstrap/Generated/LightOrmMysql)

## Running tests

There are two ways to run tests:
1) By using docker containers:
    - Copy `.env.dist` to `.env` and adjust defined values for your needs
    - Execute `make start_dev`
    - Execute `make test`
2) By using your local php and mysql database environment:
    - Ensure, that your php version is `5.6+`
    - Execute `composer install`
    - Execute `vendor/bin/phpcs -p ./src -p ./tests --standard=PHPCompatibility,PSR2 --runtime-set testVersion 5.6-; vendor/bin/phpunit -c phpunit.xml; vendor/bin/behat;`

## TODO

1) Add support for other DMS than MySQL (preferably - Postgres)
2) Add migration support
3) Add event dispatcher
